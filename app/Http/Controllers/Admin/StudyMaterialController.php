<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudyMaterialRequest;
use App\Models\Batch;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Repositories\StudyMaterialRepository;
use App\Services\ExportService;
use App\Services\StudyMaterialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudyMaterialController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected StudyMaterialRepository $materials,
        protected StudyMaterialService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', StudyMaterial::class);

        if ($request->ajax()) {
            $query = $this->materials->filtered($request->user(), $request->only([
                'search', 'file_type', 'subject_id', 'status',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($m) => $m->id,
                'title' => fn ($m) => view('admin.study-materials.partials.title', ['material' => $m])->render(),
                'subject' => fn ($m) => e($m->subject?->name ?? '—'),
                'file_type' => fn ($m) => '<span class="badge text-bg-secondary-subtle text-uppercase">'.e($m->file_type).'</span>',
                'size' => fn ($m) => $m->human_size,
                'downloads' => fn ($m) => $m->download_count,
                'uploader' => fn ($m) => e($m->uploader?->name ?? '—'),
                'status' => fn ($m) => status_badge($m->is_active),
                'actions' => fn ($m) => view('admin.study-materials.partials.actions', ['material' => $m])->render(),
            ], ['id', 'title', null, 'file_type', 'size', 'download_count', null, 'is_active']);
        }

        return view('admin.study-materials.index', [
            'subjects' => Subject::visibleTo($request->user())->active()->orderBy('name')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', StudyMaterial::class);

        return view('admin.study-materials.create', $this->formOptions($request));
    }

    public function store(StudyMaterialRequest $request): RedirectResponse
    {
        $this->authorize('create', StudyMaterial::class);

        $data = $request->validated();
        $data['file'] = $request->file('file');
        $data['coaching_id'] = $this->resolveCoachingId($request);

        $material = $this->service->create($data, $request->user());

        return redirect()->route('admin.study-materials.index')
            ->with('success', "Material \"{$material->title}\" uploaded.");
    }

    public function edit(Request $request, StudyMaterial $studyMaterial): View
    {
        $this->authorize('update', $studyMaterial);

        $studyMaterial->load('targets');

        return view('admin.study-materials.edit', ['material' => $studyMaterial] + $this->formOptions($request));
    }

    public function update(StudyMaterialRequest $request, StudyMaterial $studyMaterial): RedirectResponse
    {
        $this->authorize('update', $studyMaterial);

        $data = $request->validated();
        $data['file'] = $request->file('file');

        $this->service->update($studyMaterial, $data);

        return redirect()->route('admin.study-materials.index')
            ->with('success', "Material \"{$studyMaterial->title}\" updated.");
    }

    public function destroy(StudyMaterial $studyMaterial): JsonResponse
    {
        $this->authorize('delete', $studyMaterial);

        $this->service->delete($studyMaterial);

        return response()->json(['message' => "Material \"{$studyMaterial->title}\" deleted."]);
    }

    public function download(StudyMaterial $studyMaterial): StreamedResponse
    {
        $this->authorize('view', $studyMaterial);

        abort_unless(Storage::disk('public')->exists($studyMaterial->file_path), 404);

        $this->service->recordDownload($studyMaterial);

        return Storage::disk('public')->download(
            $studyMaterial->file_path,
            \Illuminate\Support\Str::slug($studyMaterial->title).'.'.pathinfo($studyMaterial->file_path, PATHINFO_EXTENSION),
        );
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', StudyMaterial::class);

        $rows = $this->materials->filtered($request->user(), $request->only(['search', 'file_type', 'subject_id', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($m) => [
                $m->id, $m->title, $m->subject?->name, strtoupper($m->file_type),
                $m->human_size, $m->download_count, $m->uploader?->name,
                $m->is_active ? 'Active' : 'Inactive',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'study-materials-'.now()->format('Ymd-His'),
            'Study Materials',
            ['ID', 'Title', 'Subject', 'Type', 'Size', 'Downloads', 'Uploaded By', 'Status'],
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
