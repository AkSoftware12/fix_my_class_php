<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubjectRequest;
use App\Models\Coaching;
use App\Models\Subject;
use App\Repositories\SubjectRepository;
use App\Services\ExportService;
use App\Services\SubjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected SubjectRepository $subjects,
        protected SubjectService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Subject::class);

        if ($request->ajax()) {
            $query = $this->subjects->filtered($request->user(), $request->only(['search', 'status']));

            return $this->dataTable($request, $query, [
                'id' => fn ($s) => $s->id,
                'name' => fn ($s) => e($s->name),
                'code' => fn ($s) => $s->code ? '<span class="badge text-bg-primary-subtle">'.e($s->code).'</span>' : '—',
                'coaching' => fn ($s) => e($s->coaching?->name ?? '—'),
                'status' => fn ($s) => status_badge($s->is_active),
                'actions' => fn ($s) => view('admin.subjects.partials.actions', ['subject' => $s])->render(),
            ], ['id', 'name', 'code', null, 'is_active']);
        }

        $coachings = $request->user()->coaching_id
            ? collect()
            : Coaching::visibleTo($request->user())->active()->orderBy('name')->get();

        return view('admin.subjects.index', compact('coachings'));
    }

    public function store(SubjectRequest $request): JsonResponse
    {
        $this->authorize('create', Subject::class);

        $data = $request->validated();
        $data['coaching_id'] = $request->user()->coaching_id ?? $data['coaching_id'] ?? null;
        abort_unless($data['coaching_id'], 422, 'Please select a coaching.');

        $subject = $this->service->create($data);

        return response()->json(['message' => "Subject \"{$subject->name}\" created.", 'subject' => $subject], 201);
    }

    public function edit(Subject $subject): JsonResponse
    {
        $this->authorize('update', $subject);

        return response()->json(['subject' => $subject]);
    }

    public function update(SubjectRequest $request, Subject $subject): JsonResponse
    {
        $this->authorize('update', $subject);

        $subject = $this->service->update($subject, $request->validated());

        return response()->json(['message' => "Subject \"{$subject->name}\" updated.", 'subject' => $subject]);
    }

    public function destroy(Subject $subject): JsonResponse
    {
        $this->authorize('delete', $subject);

        $this->service->delete($subject);

        return response()->json(['message' => "Subject \"{$subject->name}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Subject::class);

        $rows = $this->subjects->filtered($request->user(), $request->only(['search', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($s) => [
                $s->id, $s->name, $s->code, $s->coaching?->name,
                $s->is_active ? 'Active' : 'Inactive',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'subjects-'.now()->format('Ymd-His'),
            'Subjects',
            ['ID', 'Name', 'Code', 'Coaching', 'Status'],
            $rows,
        );
    }
}
