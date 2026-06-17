<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Repositories\CityRepository;
use App\Services\CityService;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected CityRepository $cities,
        protected CityService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', City::class);

        if ($request->ajax()) {
            $query = $this->cities->filtered($request->user(), $request->only(['search', 'status']));

            return $this->dataTable($request, $query, [
                'id' => fn ($c) => $c->id,
                'name' => fn ($c) => e($c->name),
                'state' => fn ($c) => e($c->state),
                'coachings_count' => fn ($c) => $c->coachings_count,
                'status' => fn ($c) => status_badge($c->is_active),
                'created_at' => fn ($c) => $c->created_at->format('d M Y'),
                'actions' => fn ($c) => view('admin.cities.partials.actions', ['city' => $c])->render(),
            ], ['id', 'name', 'state', 'coachings_count', 'is_active', 'created_at']);
        }

        $user = $request->user();
        $userCity = $user->hasRole('City Admin') ? $user->load('city')->city : null;

        return view('admin.cities.index', compact('userCity'));
    }

    public function store(CityRequest $request): JsonResponse
    {
        $this->authorize('create', City::class);

        $city = $this->service->create($request->validated());

        return response()->json(['message' => "City \"{$city->name}\" created.", 'city' => $city], 201);
    }

    public function edit(City $city): JsonResponse
    {
        $this->authorize('update', $city);

        return response()->json(['city' => $city]);
    }

    public function update(CityRequest $request, City $city): JsonResponse
    {
        $this->authorize('update', $city);

        $city = $this->service->update($city, $request->validated());

        return response()->json(['message' => "City \"{$city->name}\" updated.", 'city' => $city]);
    }

    public function destroy(City $city): JsonResponse
    {
        $this->authorize('delete', $city);

        $this->service->delete($city);

        return response()->json(['message' => "City \"{$city->name}\" deleted."]);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', City::class);

        $rows = $this->cities->filtered($request->user(), $request->only(['search', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($c) => [
                $c->id, $c->name, $c->state, $c->coachings_count,
                $c->is_active ? 'Active' : 'Inactive', $c->created_at->format('d M Y'),
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'cities-'.now()->format('Ymd-His'),
            'Cities',
            ['ID', 'Name', 'State', 'Coachings', 'Status', 'Created'],
            $rows,
        );
    }
}
