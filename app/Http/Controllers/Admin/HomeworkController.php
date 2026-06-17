<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\HomeworkRequest;
use App\Http\Requests\HomeworkReviewRequest;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Repositories\HomeworkRepository;
use App\Services\ExportService;
use App\Services\HomeworkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeworkController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected HomeworkRepository $homework,
        protected HomeworkService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Homework::class);

        if ($request->ajax()) {
            $query = $this->homework->filtered($request->user(), $request->only([
                'search', 'type', 'status', 'subject_id', 'due_date',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($h) => $h->id,
                'title' => fn ($h) => view('admin.homework.partials.title', ['homework' => $h])->render(),
                'subject' => fn ($h) => e($h->subject?->name ?? '—'),
                'type' => fn ($h) => '<span class="badge text-bg-secondary-subtle text-uppercase">'.e($h->type).'</span>',
                'creator' => fn ($h) => e($h->creator?->name ?? '—'),
                'due_date' => fn ($h) => $h->due_date?->format('d M Y') ?? '—',
                'submissions' => fn ($h) => $h->submissions_count,
                'status' => fn ($h) => view('admin.homework.partials.status', ['status' => $h->status])->render(),
                'actions' => fn ($h) => view('admin.homework.partials.actions', ['homework' => $h])->render(),
            ], ['id', 'title', null, 'type', null, 'due_date', 'submissions', 'status']);
        }

        return view('admin.homework.index', [
            'subjects' => Subject::visibleTo($request->user())->active()->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Homework::class);

        return view('admin.homework.create', $this->formOptions($request));
    }

    public function store(HomeworkRequest $request): RedirectResponse
    {
        $this->authorize('create', Homework::class);

        $data = $request->validated();
        $data['attachment'] = $request->file('attachment');
        $data['coaching_id'] = $this->resolveCoachingId($request);

        $homework = $this->service->create($data, $request->user());

        return redirect()->route('admin.homework.index')
            ->with('success', "Homework \"{$homework->title}\" assigned.");
    }

    public function show(Homework $homework): View
    {
        $this->authorize('view', $homework);

        $homework->load(['creator', 'subject', 'targets', 'submissions.student.user', 'submissions.reviewer']);

        return view('admin.homework.show', ['homework' => $homework]);
    }

    public function edit(Request $request, Homework $homework): View
    {
        $this->authorize('update', $homework);

        $homework->load('targets');

        return view('admin.homework.edit', ['homework' => $homework] + $this->formOptions($request));
    }

    public function update(HomeworkRequest $request, Homework $homework): RedirectResponse
    {
        $this->authorize('update', $homework);

        $data = $request->validated();
        $data['attachment'] = $request->file('attachment');

        $this->service->update($homework, $data);

        return redirect()->route('admin.homework.index')
            ->with('success', "Homework \"{$homework->title}\" updated.");
    }

    public function destroy(Homework $homework): JsonResponse
    {
        $this->authorize('delete', $homework);

        $this->service->delete($homework);

        return response()->json(['message' => "Homework \"{$homework->title}\" deleted."]);
    }

    public function reviewSubmission(HomeworkReviewRequest $request, Homework $homework, HomeworkSubmission $submission): JsonResponse
    {
        $this->authorize('update', $homework);

        abort_unless($submission->homework_id === $homework->id, 404);

        $this->service->reviewSubmission($submission, $request->validated(), $request->user());

        return response()->json(['message' => 'Submission reviewed.']);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Homework::class);

        $rows = $this->homework->filtered($request->user(), $request->only(['search', 'type', 'status', 'subject_id', 'due_date']))
            ->latest()
            ->lazy()
            ->map(fn ($h) => [
                $h->id, $h->title, $h->subject?->name, strtoupper($h->type),
                ucfirst($h->visibility), $h->creator?->name,
                $h->due_date?->format('d M Y'), ucfirst($h->status), $h->submissions_count,
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'homework-'.now()->format('Ymd-His'),
            'Homework',
            ['ID', 'Title', 'Subject', 'Type', 'Visibility', 'Created By', 'Due', 'Status', 'Submissions'],
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
            'students' => Student::visibleTo($user)->active()->with('user')->get(),
        ];
    }

    protected function resolveCoachingId(Request $request): int
    {
        if ($request->user()->coaching_id) {
            return $request->user()->coaching_id;
        }

        foreach ($request->input('targets', []) as $target) {
            [$type, $id] = array_pad(explode(':', $target, 2), 2, null);
            if (! is_numeric($id)) {
                continue;
            }

            $coachingId = match ($type) {
                'coaching' => (int) $id,
                'branch'   => Branch::find($id)?->coaching_id,
                'batch'    => Batch::find($id)?->coaching_id,
                'student'  => Student::find($id)?->coaching_id,
                default    => null,
            };

            if ($coachingId) {
                return $coachingId;
            }
        }

        abort(422, 'Could not determine coaching. Please select a coaching, branch, batch or student target.');
    }
}
