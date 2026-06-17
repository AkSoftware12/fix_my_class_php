<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NearbyController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'lat'    => ['required', 'numeric', 'between:-90,90'],
            'lng'    => ['required', 'numeric', 'between:-180,180'],
            'radius' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
        ]);

        $lat    = (float) $request->input('lat');
        $lng    = (float) $request->input('lng');
        $radius = (float) ($request->input('radius', 10));

        $branches = Branch::with(['coaching:id,name,logo_path,address,mobile,email'])
            ->nearby($lat, $lng, $radius)
            ->where('is_active', true)
            ->whereHas('coaching', fn ($q) => $q->where('is_active', true))
            ->get()
            ->map(fn ($branch) => [
                'branch_id'     => $branch->id,
                'branch_name'   => $branch->name,
                'address'       => $branch->address,
                'contact'       => $branch->contact_number,
                'latitude'      => (float) $branch->latitude,
                'longitude'     => (float) $branch->longitude,
                'distance_km'   => round((float) $branch->distance, 2),
                'coaching' => [
                    'id'       => $branch->coaching->id,
                    'name'     => $branch->coaching->name,
                    'logo_url' => $branch->coaching->logo_url,
                    'address'  => $branch->coaching->address,
                    'mobile'   => $branch->coaching->mobile,
                    'email'    => $branch->coaching->email,
                ],
            ]);

        return response()->json([
            'success' => true,
            'count'   => $branches->count(),
            'data'    => $branches,
        ]);
    }
}
