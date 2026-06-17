<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\CoachingRequest;
use App\Models\City;
use App\Models\Coaching;
use App\Repositories\CoachingRepository;
use App\Services\CoachingService;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoachingController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected CoachingRepository $coachings,
        protected CoachingService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Coaching::class);

        if ($request->ajax()) {
            $query = $this->coachings->filtered($request->user(), $request->only(['search', 'city_id', 'status']));

            return $this->dataTable($request, $query, [
                'id' => fn ($c) => $c->id,
                'coaching' => fn ($c) => view('admin.coachings.partials.identity', ['coaching' => $c])->render(),
                'city' => fn ($c) => e($c->city?->name),
                'owner_name' => fn ($c) => e($c->owner_name),
                'contact' => fn ($c) => e($c->email).'<br><small class="text-muted">'.e($c->mobile).'</small>',
                'branches_count' => fn ($c) => $c->branches_count,
                'students_count' => fn ($c) => $c->students_count,
                'status' => fn ($c) => status_badge($c->is_active),
                'actions' => fn ($c) => view('admin.coachings.partials.actions', ['coaching' => $c])->render(),
            ], ['id', 'name', null, 'owner_name', 'email', 'branches_count', 'students_count', 'is_active']);
        }

        return view('admin.coachings.index', [
            'cities' => City::active()->visibleTo(auth()->user())->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Coaching::class);

        return view('admin.coachings.create', [
            'cities' => City::active()->visibleTo(auth()->user())->orderBy('name')->get(),
        ]);
    }

    public function store(CoachingRequest $request): RedirectResponse
    {
        $this->authorize('create', Coaching::class);

        $data = $request->validated();
        $data['logo'] = $request->file('logo');

        $coaching = $this->service->create($data);

        return redirect()->route('admin.coachings.index')
            ->with('success', "Coaching \"{$coaching->name}\" created.");
    }

    public function show(Coaching $coaching): View
    {
        $this->authorize('view', $coaching);

        $coaching->load(['city', 'activeSubscription.plan'])
            ->loadCount(['branches', 'teachers', 'students']);

        return view('admin.coachings.show', ['coaching' => $coaching]);
    }

    public function edit(Coaching $coaching): View
    {
        $this->authorize('update', $coaching);

        return view('admin.coachings.edit', [
            'coaching' => $coaching,
            'cities' => City::active()->visibleTo(auth()->user())->orderBy('name')->get(),
        ]);
    }

    public function update(CoachingRequest $request, Coaching $coaching): RedirectResponse
    {
        $this->authorize('update', $coaching);

        $data = $request->validated();
        $data['logo'] = $request->file('logo');

        $this->service->update($coaching, $data);

        return redirect()->route('admin.coachings.index')
            ->with('success', "Coaching \"{$coaching->name}\" updated.");
    }

    public function destroy(Coaching $coaching): JsonResponse
    {
        $this->authorize('delete', $coaching);

        $this->service->delete($coaching);

        return response()->json(['message' => "Coaching \"{$coaching->name}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Coaching::class);

        $rows = $this->coachings->filtered($request->user(), $request->only(['search', 'city_id', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($c) => [
                $c->id, $c->name, $c->city?->name, $c->owner_name, $c->email, $c->mobile,
                $c->branches_count, $c->students_count, $c->is_active ? 'Active' : 'Inactive',
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'coachings-'.now()->format('Ymd-His'),
            'Coaching Centres',
            ['ID', 'Name', 'City', 'Owner', 'Email', 'Mobile', 'Branches', 'Students', 'Status'],
            $rows,
        );
    }
}
