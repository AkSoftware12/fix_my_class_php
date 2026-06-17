<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\TeacherRequest;
use App\Models\Branch;
use App\Models\Subject;
use App\Models\Teacher;
use App\Repositories\TeacherRepository;
use App\Services\ExportService;
use App\Services\TeacherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected TeacherRepository $teachers,
        protected TeacherService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Teacher::class);

        if ($request->ajax()) {
            $query = $this->teachers->filtered($request->user(), $request->only([
                'search', 'branch_id', 'subject_id', 'status',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($t) => $t->id,
                'teacher' => fn ($t) => view('admin.teachers.partials.identity', ['teacher' => $t])->render(),
                'mobile' => fn ($t) => e($t->user?->mobile ?? '—'),
                'qualification' => fn ($t) => e($t->qualification ?? '—'),
                'subject' => fn ($t) => e($t->subject?->name ?? '—'),
                'branch' => fn ($t) => e($t->branch?->name ?? '—'),
                'status' => fn ($t) => status_badge($t->is_active),
                'actions' => fn ($t) => view('admin.teachers.partials.actions', ['teacher' => $t])->render(),
            ], ['id', null, null, 'qualification', null, null, 'is_active']);
        }

        return view('admin.teachers.index', $this->formOptions($request));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Teacher::class);

        return view('admin.teachers.create', $this->formOptions($request));
    }

    public function store(TeacherRequest $request): RedirectResponse
    {
        $this->authorize('create', Teacher::class);

        $teacher = $this->service->create($request->validated());

        return redirect()->route('admin.teachers.index')
            ->with('success', "Teacher \"{$teacher->user->name}\" created.");
    }

    public function show(Teacher $teacher): View
    {
        $this->authorize('view', $teacher);

        $teacher->load(['user', 'branch', 'subject', 'batches.schoolClass']);

        return view('admin.teachers.show', ['teacher' => $teacher]);
    }

    public function edit(Request $request, Teacher $teacher): View
    {
        $this->authorize('update', $teacher);

        $teacher->load('user');

        return view('admin.teachers.edit', ['teacher' => $teacher] + $this->formOptions($request));
    }

    public function update(TeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $this->authorize('update', $teacher);

        $this->service->update($teacher, $request->validated());

        return redirect()->route('admin.teachers.index')
            ->with('success', "Teacher \"{$teacher->user->name}\" updated.");
    }

    public function destroy(Teacher $teacher): JsonResponse
    {
        $this->authorize('delete', $teacher);

        $name = $teacher->user?->name;
        $this->service->delete($teacher);

        return response()->json(['message' => "Teacher \"{$name}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Teacher::class);

        $rows = $this->teachers->filtered($request->user(), $request->only(['search', 'branch_id', 'subject_id', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($t) => [
                $t->id, $t->user?->name, $t->user?->mobile, $t->user?->email,
                $t->qualification, $t->subject?->name, $t->branch?->name,
                $t->is_active ? 'Active' : 'Inactive',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'teachers-'.now()->format('Ymd-His'),
            'Teachers',
            ['ID', 'Name', 'Mobile', 'Email', 'Qualification', 'Subject', 'Branch', 'Status'],
            $rows,
        );
    }

    protected function formOptions(Request $request): array
    {
        return [
            'branches' => Branch::visibleTo($request->user())->active()->orderBy('name')->get(),
            'subjects' => Subject::visibleTo($request->user())->active()->orderBy('name')->get(),
        ];
    }
}
