<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\OnlineClassRequest;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\OnlineClass;
use App\Models\Teacher;
use App\Repositories\OnlineClassRepository;
use App\Services\ExportService;
use App\Services\OnlineClassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnlineClassController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected OnlineClassRepository $classes,
        protected OnlineClassService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', OnlineClass::class);

        if ($request->ajax()) {
            $query = $this->classes->filtered($request->user(), $request->only([
                'search', 'status', 'teacher_id', 'class_date',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($c) => $c->id,
                'title' => fn ($c) => e($c->title),
                'teacher' => fn ($c) => e($c->teacher?->user?->name ?? '—'),
                'batch' => fn ($c) => e($c->batch?->name ?? '—'),
                'schedule' => fn ($c) => $c->class_date->format('d M Y').' '.substr($c->start_time, 0, 5),
                'link' => fn ($c) => '<a href="'.e($c->meeting_link).'" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="bi bi-camera-video"></i> Join</a>',
                'status' => fn ($c) => view('admin.online-classes.partials.status', ['status' => $c->status])->render(),
                'actions' => fn ($c) => view('admin.online-classes.partials.actions', ['onlineClass' => $c])->render(),
            ], ['id', 'title', null, null, 'class_date', null, 'status']);
        }

        return view('admin.online-classes.index', [
            'teachers' => Teacher::visibleTo($request->user())->active()->with('user')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', OnlineClass::class);

        return view('admin.online-classes.create', $this->formOptions($request));
    }

    public function store(OnlineClassRequest $request): RedirectResponse
    {
        $this->authorize('create', OnlineClass::class);

        $data = $request->validated();
        $teacher = Teacher::findOrFail($data['teacher_id']);
        $data['coaching_id'] = $teacher->coaching_id;
        $data['branch_id'] = $data['branch_id'] ?? $teacher->branch_id;

        $onlineClass = $this->service->create($data);

        return redirect()->route('admin.online-classes.index')
            ->with('success', "Online class \"{$onlineClass->title}\" scheduled.");
    }

    public function edit(Request $request, OnlineClass $onlineClass): View
    {
        $this->authorize('update', $onlineClass);

        return view('admin.online-classes.edit', ['onlineClass' => $onlineClass] + $this->formOptions($request));
    }

    public function update(OnlineClassRequest $request, OnlineClass $onlineClass): RedirectResponse
    {
        $this->authorize('update', $onlineClass);

        $this->service->update($onlineClass, $request->validated());

        return redirect()->route('admin.online-classes.index')
            ->with('success', "Online class \"{$onlineClass->title}\" updated.");
    }

    public function destroy(OnlineClass $onlineClass): JsonResponse
    {
        $this->authorize('delete', $onlineClass);

        $this->service->delete($onlineClass);

        return response()->json(['message' => "Online class \"{$onlineClass->title}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', OnlineClass::class);

        $rows = $this->classes->filtered($request->user(), $request->only(['search', 'status', 'teacher_id', 'class_date']))
            ->latest()
            ->lazy()
            ->map(fn ($c) => [
                $c->id, $c->title, $c->teacher?->user?->name, $c->batch?->name,
                $c->class_date->format('d M Y'), $c->start_time, $c->end_time,
                $c->meeting_link, ucfirst($c->status),
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'online-classes-'.now()->format('Ymd-His'),
            'Online Classes',
            ['ID', 'Title', 'Teacher', 'Batch', 'Date', 'Start', 'End', 'Link', 'Status'],
            $rows,
        );
    }

    protected function formOptions(Request $request): array
    {
        $user = $request->user();

        return [
            'teachers' => Teacher::visibleTo($user)->active()->with('user')->get(),
            'branches' => Branch::visibleTo($user)->active()->orderBy('name')->get(),
            'batches' => Batch::visibleTo($user)->active()->orderBy('name')->get(),
        ];
    }
}
