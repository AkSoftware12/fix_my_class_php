<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExamRequest;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Repositories\ExamRepository;
use App\Services\ExamService;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected ExamRepository $exams,
        protected ExamService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Exam::class);

        if ($request->ajax()) {
            $query = $this->exams->filtered($request->user(), $request->only([
                'search', 'type', 'status', 'subject_id',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($e) => $e->id,
                'title' => fn ($e) => e($e->title),
                'type' => fn ($e) => '<span class="badge text-bg-secondary-subtle text-uppercase">'.e($e->type).'</span>',
                'subject' => fn ($e) => e($e->subject?->name ?? '—'),
                'class_batch' => fn ($e) => e(trim(($e->schoolClass?->name ?? '').' / '.($e->batch?->name ?? ''), ' /') ?: '—'),
                'exam_date' => fn ($e) => $e->exam_date?->format('d M Y') ?? '—',
                'marks' => fn ($e) => $e->total_marks.' / pass '.$e->passing_marks,
                'questions_count' => fn ($e) => $e->questions_count,
                'status' => fn ($e) => view('admin.exams.partials.status', ['status' => $e->status])->render(),
                'actions' => fn ($e) => view('admin.exams.partials.actions', ['exam' => $e])->render(),
            ], ['id', 'title', 'type', null, null, 'exam_date', 'total_marks', 'questions_count', 'status']);
        }

        return view('admin.exams.index', [
            'subjects' => Subject::visibleTo($request->user())->active()->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Exam::class);

        return view('admin.exams.create', $this->formOptions($request));
    }

    public function store(ExamRequest $request): RedirectResponse
    {
        $this->authorize('create', Exam::class);

        $data = $request->validated();
        $data['coaching_id'] = $request->user()->coaching_id
            ?? Branch::find($data['branch_id'] ?? null)?->coaching_id
            ?? Batch::find($data['batch_id'] ?? null)?->coaching_id
            ?? abort(422, 'Please select a branch or batch to associate this exam with a coaching.');

        $exam = $this->service->create($data, $request->user());

        return redirect()->route('admin.exams.index')
            ->with('success', "Exam \"{$exam->title}\" created.");
    }

    public function show(Exam $exam): View
    {
        $this->authorize('view', $exam);

        $exam->load(['subject', 'schoolClass', 'batch', 'questions', 'creator'])
            ->loadCount('results');

        return view('admin.exams.show', ['exam' => $exam]);
    }

    public function edit(Request $request, Exam $exam): View
    {
        $this->authorize('update', $exam);

        $exam->load('questions');

        return view('admin.exams.edit', ['exam' => $exam] + $this->formOptions($request));
    }

    public function update(ExamRequest $request, Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);

        $this->service->update($exam, $request->validated());

        return redirect()->route('admin.exams.index')
            ->with('success', "Exam \"{$exam->title}\" updated.");
    }

    public function destroy(Exam $exam): JsonResponse
    {
        $this->authorize('delete', $exam);

        $this->service->delete($exam);

        return response()->json(['message' => "Exam \"{$exam->title}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Exam::class);

        $rows = $this->exams->filtered($request->user(), $request->only(['search', 'type', 'status', 'subject_id']))
            ->latest()
            ->lazy()
            ->map(fn ($e) => [
                $e->id, $e->title, strtoupper($e->type), $e->subject?->name,
                $e->exam_date?->format('d M Y'), $e->total_marks, $e->passing_marks,
                $e->questions_count, $e->results_count, ucfirst($e->status),
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'exams-'.now()->format('Ymd-His'),
            'Exams',
            ['ID', 'Title', 'Type', 'Subject', 'Date', 'Total Marks', 'Passing', 'Questions', 'Results', 'Status'],
            $rows,
        );
    }

    protected function formOptions(Request $request): array
    {
        $user = $request->user();

        return [
            'subjects' => Subject::visibleTo($user)->active()->orderBy('name')->get(),
            'branches' => Branch::visibleTo($user)->active()->orderBy('name')->get(),
            'classes' => SchoolClass::visibleTo($user)->active()->orderBy('name')->get(),
            'batches' => Batch::visibleTo($user)->active()->orderBy('name')->get(),
        ];
    }
}
