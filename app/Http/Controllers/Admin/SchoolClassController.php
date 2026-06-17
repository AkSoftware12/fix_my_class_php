<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolClassRequest;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Repositories\SchoolClassRepository;
use App\Services\ExportService;
use App\Services\SchoolClassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected SchoolClassRepository $classes,
        protected SchoolClassService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', SchoolClass::class);

        if ($request->ajax()) {
            $query = $this->classes->filtered($request->user(), $request->only(['search', 'branch_id', 'status']));

            return $this->dataTable($request, $query, [
                'id' => fn ($c) => $c->id,
                'name' => fn ($c) => e($c->name),
                'branch' => fn ($c) => e($c->branch?->name ?? 'All branches'),
                'batches_count' => fn ($c) => $c->batches_count,
                'students_count' => fn ($c) => $c->students_count,
                'status' => fn ($c) => status_badge($c->is_active),
                'actions' => fn ($c) => view('admin.classes.partials.actions', ['class' => $c])->render(),
            ], ['id', 'name', null, 'batches_count', 'students_count', 'is_active']);
        }

        return view('admin.classes.index', [
            'branches' => Branch::visibleTo($request->user())->active()->orderBy('name')->get(),
        ]);
    }

    public function store(SchoolClassRequest $request): JsonResponse
    {
        $this->authorize('create', SchoolClass::class);

        $data = $request->validated();
        $data['coaching_id'] = $this->resolveCoachingId($request);

        $class = $this->service->create($data);

        return response()->json(['message' => "Class \"{$class->name}\" created.", 'class' => $class], 201);
    }

    public function edit(SchoolClass $class): JsonResponse
    {
        $this->authorize('update', $class);

        return response()->json(['class' => $class]);
    }

    public function update(SchoolClassRequest $request, SchoolClass $class): JsonResponse
    {
        $this->authorize('update', $class);

        $class = $this->service->update($class, $request->validated());

        return response()->json(['message' => "Class \"{$class->name}\" updated.", 'class' => $class]);
    }

    public function destroy(SchoolClass $class): JsonResponse
    {
        $this->authorize('delete', $class);

        $this->service->delete($class);

        return response()->json(['message' => "Class \"{$class->name}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', SchoolClass::class);

        $rows = $this->classes->filtered($request->user(), $request->only(['search', 'branch_id', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($c) => [
                $c->id, $c->name, $c->branch?->name ?? 'All branches',
                $c->batches_count, $c->students_count,
                $c->is_active ? 'Active' : 'Inactive',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'classes-'.now()->format('Ymd-His'),
            'Classes',
            ['ID', 'Name', 'Branch', 'Batches', 'Students', 'Status'],
            $rows,
        );
    }

    protected function resolveCoachingId(Request $request): int
    {
        $user = $request->user();

        if ($user->coaching_id) {
            return $user->coaching_id;
        }

        $branchId = $request->input('branch_id');
        $branch = $branchId ? Branch::find($branchId) : null;

        abort_unless($branch, 422, 'A branch is required to determine the coaching.');

        return $branch->coaching_id;
    }
}
