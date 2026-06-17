<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use App\Models\Coaching;
use App\Repositories\BannerRepository;
use App\Services\BannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected BannerRepository $banners,
        protected BannerService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Banner::class);

        if ($request->ajax()) {
            $query = $this->banners->filtered($request->user(), $request->only(['search', 'coaching_id', 'status']));

            return $this->dataTable($request, $query, [
                'id'       => fn ($b) => $b->id,
                'image'    => fn ($b) => $b->image_url
                    ? '<img src="'.e($b->image_url).'" style="height:40px;border-radius:4px;" alt="">'
                    : '<span class="text-muted">—</span>',
                'title'    => fn ($b) => e($b->title),
                'coaching' => fn ($b) => e($b->coaching?->name ?? '— Global —'),
                'period'   => fn ($b) => ($b->starts_at ? $b->starts_at->format('d M Y') : '∞')
                    .' → '.($b->ends_at ? $b->ends_at->format('d M Y') : '∞'),
                'sort_order' => fn ($b) => $b->sort_order,
                'status'   => fn ($b) => status_badge($b->is_active),
                'actions'  => fn ($b) => view('admin.banners.partials.actions', ['banner' => $b])->render(),
            ], ['id', null, 'title', null, null, 'sort_order', 'is_active']);
        }

        $user = $request->user();
        $coachings = Coaching::visibleTo($user)->active()->orderBy('name')->get();

        return view('admin.banners.index', compact('coachings'));
    }

    public function store(BannerRequest $request): JsonResponse
    {
        $this->authorize('create', Banner::class);

        $data = $request->validated();
        $data['image'] = $request->file('image');

        $banner = $this->service->create($data);

        return response()->json(['message' => "Banner \"{$banner->title}\" created.", 'banner' => $banner], 201);
    }

    public function edit(Banner $banner): JsonResponse
    {
        $this->authorize('update', $banner);

        return response()->json(['banner' => $banner]);
    }

    public function update(BannerRequest $request, Banner $banner): JsonResponse
    {
        $this->authorize('update', $banner);

        $data = $request->validated();
        $data['image'] = $request->file('image');

        $this->service->update($banner, $data);

        return response()->json(['message' => "Banner \"{$banner->title}\" updated."]);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        $this->authorize('delete', $banner);

        $this->service->delete($banner);

        return response()->json(['message' => "Banner \"{$banner->title}\" deleted."]);
    }
}
