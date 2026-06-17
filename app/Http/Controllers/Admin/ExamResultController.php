<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExamResultRequest;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Student;
use App\Services\ExamResultService;
use App\Services\ExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExamResultController extends Controller
{
    public function __construct(protected ExamResultService $service)
    {
    }

    /**
     * Marks-entry sheet for an exam.
     */
    public function entry(Request $request, Exam $exam): View
    {
        $this->authorize('update', $exam);

        $students = Student::query()
            ->visibleTo($request->user())
            ->active()
            ->with('user')
            ->when($exam->batch_id, fn ($q) => $q->where(fn ($w) => $w
                ->where('batch_id', $exam->batch_id)
                ->orWhereHas('batches', fn ($b) => $b->where('batches.id', $exam->batch_id))))
            ->when(! $exam->batch_id && $exam->school_class_id, fn ($q) => $q->where('school_class_id', $exam->school_class_id))
            ->when(! $exam->batch_id && ! $exam->school_class_id && $exam->branch_id, fn ($q) => $q->where('branch_id', $exam->branch_id))
            ->get();

        $existing = $exam->results()->get()->keyBy('student_id');

        return view('admin.exams.results', [
            'exam' => $exam,
            'students' => $students,
            'existing' => $existing,
        ]);
    }

    public function store(ExamResultRequest $request, Exam $exam): RedirectResponse
    {
        $this->authorize('update', $exam);

        $this->service->saveResults($exam, $request->validated('results'), $request->user());

        return redirect()->route('admin.exams.results.entry', $exam)
            ->with('success', 'Results saved.');
    }

    public function publish(Exam $exam): JsonResponse
    {
        $this->authorize('update', $exam);

        if (! $exam->results()->exists()) {
            return response()->json(['message' => 'Enter results before publishing.'], 422);
        }

        $this->service->publish($exam);

        return response()->json(['message' => "Results published for \"{$exam->title}\"."]);
    }

    public function unpublish(Exam $exam): JsonResponse
    {
        $this->authorize('update', $exam);

        $this->service->unpublish($exam);

        return response()->json(['message' => "Results unpublished for \"{$exam->title}\"."]);
    }

    /**
     * Printable marksheet for a single student result.
     */
    public function marksheet(Exam $exam, ExamResult $result)
    {
        $this->authorize('view', $exam);

        abort_unless($result->exam_id === $exam->id, 404);

        $result->load(['student.user', 'student.branch.coaching', 'student.schoolClass']);

        $pdf = Pdf::loadView('admin.exams.marksheet', [
            'exam' => $exam,
            'result' => $result,
        ])->setPaper('a4');

        return $pdf->download('marksheet-'.$result->student->admission_number.'.pdf');
    }

    public function export(Request $request, Exam $exam, ExportService $export)
    {
        $this->authorize('export', Exam::class);

        $rows = $exam->results()
            ->with('student.user')
            ->orderBy('rank')
            ->lazy()
            ->map(fn ($r) => [
                $r->rank, $r->student?->admission_number, $r->student?->user?->name,
                $r->marks_obtained, $exam->total_marks, $r->grade,
                $r->is_pass ? 'Pass' : 'Fail', $r->is_published ? 'Published' : 'Draft',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'results-'.\Illuminate\Support\Str::slug($exam->title).'-'.now()->format('Ymd'),
            'Results: '.$exam->title,
            ['Rank', 'Admission No', 'Student', 'Marks', 'Out Of', 'Grade', 'Result', 'Published'],
            $rows,
        );
    }
}
