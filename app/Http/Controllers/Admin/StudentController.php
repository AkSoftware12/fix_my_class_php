<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Repositories\StudentRepository;
use App\Services\ExportService;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected StudentRepository $students,
        protected StudentService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Student::class);

        if ($request->ajax()) {
            $query = $this->students->filtered($request->user(), $request->only([
                'search', 'branch_id', 'school_class_id', 'batch_id', 'status',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($s) => $s->id,
                'admission_number' => fn ($s) => '<span class="fw-semibold">'.e($s->admission_number).'</span>',
                'student' => fn ($s) => view('admin.students.partials.identity', ['student' => $s])->render(),
                'mobile' => fn ($s) => e($s->user?->mobile ?? '—'),
                'branch' => fn ($s) => e($s->branch?->name ?? '—'),
                'class' => fn ($s) => e($s->schoolClass?->name ?? '—'),
                'batch' => fn ($s) => e($s->batch?->name ?? '—'),
                'status' => fn ($s) => status_badge($s->is_active),
                'actions' => fn ($s) => view('admin.students.partials.actions', ['student' => $s])->render(),
            ], ['id', 'admission_number', null, null, null, null, null, 'is_active']);
        }

        return view('admin.students.index', $this->formOptions($request));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Student::class);

        return view('admin.students.create', $this->formOptions($request));
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        $this->authorize('create', Student::class);

        $data = $request->validated();
        $data['photo'] = $request->file('photo');
        $data['documents'] = $request->file('documents', []);

        $student = $this->service->create($data);

        return redirect()->route('admin.students.show', $student)
            ->with('success', "Student \"{$student->user->name}\" admitted with number {$student->admission_number}.");
    }

    public function show(Student $student): View
    {
        $this->authorize('view', $student);

        $student->load([
            'user', 'branch', 'schoolClass', 'batch', 'documents',
            'homeworkSubmissions.homework', 'examResults.exam',
        ]);

        return view('admin.students.show', ['student' => $student]);
    }

    public function edit(Request $request, Student $student): View
    {
        $this->authorize('update', $student);

        $student->load(['user', 'documents']);

        return view('admin.students.edit', ['student' => $student] + $this->formOptions($request));
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        $this->authorize('update', $student);

        $data = $request->validated();
        $data['photo'] = $request->file('photo');
        $data['documents'] = $request->file('documents', []);

        $this->service->update($student, $data);

        return redirect()->route('admin.students.show', $student)
            ->with('success', "Student \"{$student->user->name}\" updated.");
    }

    public function destroy(Student $student): JsonResponse
    {
        $this->authorize('delete', $student);

        $name = $student->user?->name;
        $this->service->delete($student);

        return response()->json(['message' => "Student \"{$name}\" deleted."]);
    }

    public function idCard(Student $student): View
    {
        $this->authorize('view', $student);

        $student->load(['user', 'branch.coaching', 'schoolClass', 'batch']);

        return view('admin.students.id-card', ['student' => $student]);
    }

    public function bulkIdCards(Request $request): View
    {
        $this->authorize('viewAny', Student::class);

        $ids = array_filter(explode(',', $request->query('ids', '')));

        abort_if(empty($ids), 400, 'No students selected.');

        $students = Student::whereIn('id', $ids)
            ->visibleTo($request->user())
            ->with(['user', 'branch.coaching', 'schoolClass', 'batch'])
            ->get();

        return view('admin.students.id-cards-bulk', ['students' => $students]);
    }

    public function batchIdCards(Request $request, Batch $batch): View
    {
        $this->authorize('viewAny', Student::class);

        $batch->load('branch.coaching', 'schoolClass');

        $students = Student::where('batch_id', $batch->id)
            ->visibleTo($request->user())
            ->active()
            ->with(['user', 'branch.coaching', 'schoolClass', 'batch'])
            ->orderBy('admission_number')
            ->get();

        abort_if($students->isEmpty(), 404, 'No active students in this batch.');

        return view('admin.students.id-cards-bulk', ['students' => $students, 'batch' => $batch]);
    }

    public function destroyDocument(Student $student, StudentDocument $document): JsonResponse
    {
        $this->authorize('update', $student);

        abort_unless($document->student_id === $student->id, 404);

        app(\App\Services\FileUploadService::class)->delete($document->file_path);
        $document->delete();

        return response()->json(['message' => 'Document removed.']);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Student::class);

        $rows = $this->students->filtered($request->user(), $request->only(['search', 'branch_id', 'school_class_id', 'batch_id', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($s) => [
                $s->admission_number, $s->user?->name, $s->user?->mobile, $s->user?->email,
                $s->branch?->name, $s->schoolClass?->name, $s->batch?->name,
                $s->guardian_name, $s->guardian_mobile,
                $s->is_active ? 'Active' : 'Inactive',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'students-'.now()->format('Ymd-His'),
            'Students',
            ['Admission No', 'Name', 'Mobile', 'Email', 'Branch', 'Class', 'Batch', 'Guardian', 'Guardian Mobile', 'Status'],
            $rows,
        );
    }

    protected function formOptions(Request $request): array
    {
        $user = $request->user();

        return [
            'branches' => Branch::visibleTo($user)->active()->orderBy('name')->get(),
            'classes' => SchoolClass::visibleTo($user)->active()->orderBy('name')->get(),
            'batches' => Batch::visibleTo($user)->active()->orderBy('name')->get(),
        ];
    }
}
