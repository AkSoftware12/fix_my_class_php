<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\BatchRequest;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Repositories\BatchRepository;
use App\Services\BatchService;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BatchController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected BatchRepository $batches,
        protected BatchService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Batch::class);

        if ($request->ajax()) {
            $query = $this->batches->filtered($request->user(), $request->only([
                'search', 'branch_id', 'school_class_id', 'status',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($b) => $b->id,
                'name' => fn ($b) => e($b->name),
                'branch' => fn ($b) => e($b->branch?->name ?? '—'),
                'class' => fn ($b) => e($b->schoolClass?->name ?? '—'),
                'timing' => fn ($b) => $b->start_time
                    ? substr($b->start_time, 0, 5).' – '.substr((string) $b->end_time, 0, 5)
                    : '—',
                'students_count' => fn ($b) => $b->students_count.($b->capacity ? ' / '.$b->capacity : ''),
                'teachers_count' => fn ($b) => $b->teachers_count,
                'subjects_count' => fn ($b) => $b->subjects_count,
                'status' => fn ($b) => status_badge($b->is_active),
                'actions' => fn ($b) => view('admin.batches.partials.actions', ['batch' => $b])->render(),
            ], ['id', 'name', null, null, 'start_time', 'students_count', 'teachers_count', 'subjects_count', 'is_active']);
        }

        return view('admin.batches.index', $this->formOptions($request));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Batch::class);

        return view('admin.batches.create', $this->formOptions($request, withAssignables: true));
    }

    public function store(BatchRequest $request): RedirectResponse
    {
        $this->authorize('create', Batch::class);

        $data = $request->validated();
        $data['coaching_id'] = Branch::findOrFail($data['branch_id'])->coaching_id;

        $batch = $this->service->create($data);

        return redirect()->route('admin.batches.index')
            ->with('success', "Batch \"{$batch->name}\" created.");
    }

    public function show(Batch $batch): View
    {
        $this->authorize('view', $batch);

        $batch->load(['branch', 'schoolClass', 'subjects', 'teachers.user', 'students.user']);

        return view('admin.batches.show', ['batch' => $batch]);
    }

    public function edit(Request $request, Batch $batch): View
    {
        $this->authorize('update', $batch);

        $batch->load(['subjects', 'teachers', 'students']);

        return view('admin.batches.edit', ['batch' => $batch] + $this->formOptions($request, withAssignables: true));
    }

    public function update(BatchRequest $request, Batch $batch): RedirectResponse
    {
        $this->authorize('update', $batch);

        $data = $request->validated();
        $data['coaching_id'] = Branch::findOrFail($data['branch_id'])->coaching_id;

        $this->service->update($batch, $data);

        return redirect()->route('admin.batches.index')
            ->with('success', "Batch \"{$batch->name}\" updated.");
    }

    public function destroy(Batch $batch): JsonResponse
    {
        $this->authorize('delete', $batch);

        $this->service->delete($batch);

        return response()->json(['message' => "Batch \"{$batch->name}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Batch::class);

        $rows = $this->batches->filtered($request->user(), $request->only(['search', 'branch_id', 'school_class_id', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($b) => [
                $b->id, $b->name, $b->branch?->name, $b->schoolClass?->name,
                $b->start_time, $b->end_time, $b->students_count, $b->teachers_count,
                $b->is_active ? 'Active' : 'Inactive',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'batches-'.now()->format('Ymd-His'),
            'Batches',
            ['ID', 'Name', 'Branch', 'Class', 'Start', 'End', 'Students', 'Teachers', 'Status'],
            $rows,
        );
    }

    protected function formOptions(Request $request, bool $withAssignables = false): array
    {
        $user = $request->user();

        $options = [
            'branches' => Branch::visibleTo($user)->active()->orderBy('name')->get(),
            'classes' => SchoolClass::visibleTo($user)->active()->orderBy('name')->get(),
        ];

        if ($withAssignables) {
            $options['subjects'] = Subject::visibleTo($user)->active()->orderBy('name')->get();
            $options['teachers'] = Teacher::visibleTo($user)->active()->with('user')->get();
            $options['students'] = Student::visibleTo($user)->active()->with('user')->get();
        }

        return $options;
    }
}
