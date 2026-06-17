<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\NoticeRequest;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Repositories\NoticeRepository;
use App\Services\ExportService;
use App\Services\NoticeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NoticeController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected NoticeRepository $notices,
        protected NoticeService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Notice::class);

        if ($request->ajax()) {
            $query = $this->notices->filtered($request->user(), $request->only([
                'search', 'type', 'audience', 'status',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($n) => $n->id,
                'title' => fn ($n) => e($n->title),
                'type' => fn ($n) => view('admin.notices.partials.type', ['type' => $n->type])->render(),
                'audience' => fn ($n) => ucfirst($n->audience),
                'publish_at' => fn ($n) => $n->publish_at?->format('d M Y H:i') ?? '—',
                'expires_at' => fn ($n) => $n->expires_at?->format('d M Y') ?? 'Never',
                'reads_count' => fn ($n) => $n->reads_count,
                'status' => fn ($n) => status_badge($n->is_active),
                'actions' => fn ($n) => view('admin.notices.partials.actions', ['notice' => $n])->render(),
            ], ['id', 'title', 'type', 'audience', 'publish_at', 'expires_at', 'reads_count', 'is_active']);
        }

        return view('admin.notices.index');
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Notice::class);

        return view('admin.notices.create', $this->formOptions($request));
    }

    public function store(NoticeRequest $request): RedirectResponse
    {
        $this->authorize('create', Notice::class);

        $data = $request->validated();
        $data['attachment'] = $request->file('attachment');

        $data['coaching_id'] = $this->resolveCoachingId($request);

        $notice = $this->service->create($data, $request->user());

        return redirect()->route('admin.notices.index')
            ->with('success', "Notice \"{$notice->title}\" saved.");
    }

    public function show(Notice $notice): View
    {
        $this->authorize('view', $notice);

        $notice->load(['creator', 'targets', 'reads.user']);

        return view('admin.notices.show', ['notice' => $notice]);
    }

    public function edit(Request $request, Notice $notice): View
    {
        $this->authorize('update', $notice);

        $notice->load('targets');

        return view('admin.notices.edit', ['notice' => $notice] + $this->formOptions($request));
    }

    public function update(NoticeRequest $request, Notice $notice): RedirectResponse
    {
        $this->authorize('update', $notice);

        $data = $request->validated();
        $data['attachment'] = $request->file('attachment');

        $this->service->update($notice, $data);

        return redirect()->route('admin.notices.index')
            ->with('success', "Notice \"{$notice->title}\" updated.");
    }

    public function destroy(Notice $notice): JsonResponse
    {
        $this->authorize('delete', $notice);

        $this->service->delete($notice);

        return response()->json(['message' => "Notice \"{$notice->title}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Notice::class);

        $rows = $this->notices->filtered($request->user(), $request->only(['search', 'type', 'audience', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($n) => [
                $n->id, $n->title, ucfirst($n->type), ucfirst($n->visibility), ucfirst($n->audience),
                $n->publish_at?->format('d M Y H:i'), $n->expires_at?->format('d M Y'),
                $n->reads_count, $n->is_active ? 'Active' : 'Inactive',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'notices-'.now()->format('Ymd-His'),
            'Notices',
            ['ID', 'Title', 'Type', 'Visibility', 'Audience', 'Published', 'Expires', 'Reads', 'Status'],
            $rows,
        );
    }

    protected function formOptions(Request $request): array
    {
        $user = $request->user();

        return [
            'coachings' => $user->coaching_id
                ? collect()
                : \App\Models\Coaching::visibleTo($user)->active()->orderBy('name')->get(),
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

        if ($coachingId = $request->input('coaching_id')) {
            return (int) $coachingId;
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

        abort(422, 'Please select a coaching (for public notices) or at least one target (for private notices).');
    }
}
