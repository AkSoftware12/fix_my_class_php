<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\BranchRequest;
use App\Models\Branch;
use App\Models\Coaching;
use App\Repositories\BranchRepository;
use App\Services\BranchService;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected BranchRepository $branches,
        protected BranchService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Branch::class);

        if ($request->ajax()) {
            $query = $this->branches->filtered($request->user(), $request->only(['search', 'coaching_id', 'status']));

            return $this->dataTable($request, $query, [
                'id' => fn ($b) => $b->id,
                'name' => fn ($b) => e($b->name),
                'code' => fn ($b) => '<span class="badge text-bg-primary-subtle">'.e($b->code).'</span>',
                'coaching' => fn ($b) => e($b->coaching?->name),
                'contact_number' => fn ($b) => e($b->contact_number ?? '—'),
                'students_count' => fn ($b) => $b->students_count,
                'teachers_count' => fn ($b) => $b->teachers_count,
                'status' => fn ($b) => status_badge($b->is_active),
                'actions' => fn ($b) => view('admin.branches.partials.actions', ['branch' => $b])->render(),
            ], ['id', 'name', 'code', null, 'contact_number', 'students_count', 'teachers_count', 'is_active']);
        }

        return view('admin.branches.index', [
            'coachings' => Coaching::visibleTo($request->user())->active()->orderBy('name')->get(),
        ]);
    }

    public function store(BranchRequest $request): JsonResponse
    {
        $this->authorize('create', Branch::class);

        $branch = $this->service->create($request->validated());

        return response()->json(['message' => "Branch \"{$branch->name}\" created.", 'branch' => $branch], 201);
    }

    public function edit(Branch $branch): JsonResponse
    {
        $this->authorize('update', $branch);

        return response()->json(['branch' => $branch]);
    }

    public function update(BranchRequest $request, Branch $branch): JsonResponse
    {
        $this->authorize('update', $branch);

        $branch = $this->service->update($branch, $request->validated());

        return response()->json(['message' => "Branch \"{$branch->name}\" updated.", 'branch' => $branch]);
    }

    public function destroy(Branch $branch): JsonResponse
    {
        $this->authorize('delete', $branch);

        $this->service->delete($branch);

        return response()->json(['message' => "Branch \"{$branch->name}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Branch::class);

        $rows = $this->branches->filtered($request->user(), $request->only(['search', 'coaching_id', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($b) => [
                $b->id, $b->name, $b->code, $b->coaching?->name, $b->contact_number,
                $b->students_count, $b->teachers_count, $b->is_active ? 'Active' : 'Inactive',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'branches-'.now()->format('Ymd-His'),
            'Branches',
            ['ID', 'Name', 'Code', 'Coaching', 'Contact', 'Students', 'Teachers', 'Status'],
            $rows,
        );
    }
}
