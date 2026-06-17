<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionPlanRequest;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubscriptionPlanController extends Controller
{
    use RespondsWithDataTable;

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', \App\Models\Subscription::class);

        if ($request->ajax()) {
            $query = SubscriptionPlan::query()
                ->withCount('subscriptions')
                ->when($request->input('search'), fn ($q, $s) => $q->where('name', 'like', "%{$s}%"));

            return $this->dataTable($request, $query, [
                'id' => fn ($p) => $p->id,
                'name' => fn ($p) => e($p->name),
                'price' => fn ($p) => number_format((float) $p->price, 2),
                'billing_cycle' => fn ($p) => ucwords(str_replace('_', ' ', $p->billing_cycle)),
                'limits' => fn ($p) => "{$p->max_branches} br / {$p->max_teachers} tch / {$p->max_students} std",
                'subscriptions_count' => fn ($p) => $p->subscriptions_count,
                'status' => fn ($p) => status_badge($p->is_active),
                'actions' => fn ($p) => view('admin.subscription-plans.partials.actions', ['plan' => $p])->render(),
            ], ['id', 'name', 'price', 'billing_cycle', null, 'subscriptions_count', 'is_active']);
        }

        return view('admin.subscription-plans.index');
    }

    public function store(SubscriptionPlanRequest $request): JsonResponse
    {
        $this->authorize('create', \App\Models\Subscription::class);

        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $plan = SubscriptionPlan::create($data);

        return response()->json(['message' => "Plan \"{$plan->name}\" created.", 'plan' => $plan], 201);
    }

    public function edit(SubscriptionPlan $plan): JsonResponse
    {
        $this->authorize('update', \App\Models\Subscription::class);

        return response()->json(['plan' => $plan]);
    }

    public function update(SubscriptionPlanRequest $request, SubscriptionPlan $plan): JsonResponse
    {
        $this->authorize('update', \App\Models\Subscription::class);

        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $plan->update($data);

        return response()->json(['message' => "Plan \"{$plan->name}\" updated.", 'plan' => $plan]);
    }

    public function destroy(SubscriptionPlan $plan): JsonResponse
    {
        $this->authorize('delete', \App\Models\Subscription::class);

        if ($plan->subscriptions()->exists()) {
            return response()->json(['message' => 'Cannot delete a plan with existing subscriptions.'], 422);
        }

        $plan->delete();

        return response()->json(['message' => "Plan \"{$plan->name}\" deleted."]);
    }
}
