<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsWithDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubscriptionRequest;
use App\Models\Coaching;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Repositories\SubscriptionRepository;
use App\Services\ExportService;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    use RespondsWithDataTable;

    public function __construct(
        protected SubscriptionRepository $subscriptions,
        protected SubscriptionService $service,
    ) {
    }

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Subscription::class);

        if ($request->ajax()) {
            $query = $this->subscriptions->filtered($request->user(), $request->only([
                'coaching_id', 'plan_id', 'status',
            ]));

            return $this->dataTable($request, $query, [
                'id' => fn ($s) => $s->id,
                'coaching' => fn ($s) => e($s->coaching?->name ?? '—'),
                'plan' => fn ($s) => e($s->plan?->name ?? '—'),
                'period' => fn ($s) => $s->starts_at->format('d M Y').' → '.$s->ends_at->format('d M Y'),
                'amount_paid' => fn ($s) => number_format((float) $s->amount_paid, 2),
                'status' => fn ($s) => view('admin.subscriptions.partials.status', ['status' => $s->status])->render(),
                'actions' => fn ($s) => view('admin.subscriptions.partials.actions', ['subscription' => $s])->render(),
            ], ['id', null, null, 'starts_at', 'amount_paid', 'status']);
        }

        return view('admin.subscriptions.index', $this->formOptions($request));
    }

    public function store(SubscriptionRequest $request): JsonResponse
    {
        $this->authorize('create', Subscription::class);

        $subscription = $this->service->create($request->validated());

        return response()->json(['message' => 'Subscription created.', 'subscription' => $subscription], 201);
    }

    public function edit(Subscription $subscription): JsonResponse
    {
        $this->authorize('update', $subscription);

        return response()->json(['subscription' => $subscription]);
    }

    public function update(SubscriptionRequest $request, Subscription $subscription): JsonResponse
    {
        $this->authorize('update', $subscription);

        $this->service->update($subscription, $request->validated());

        return response()->json(['message' => 'Subscription updated.']);
    }

    public function cancel(Subscription $subscription): JsonResponse
    {
        $this->authorize('update', $subscription);

        $this->service->cancel($subscription);

        return response()->json(['message' => 'Subscription cancelled.']);
    }

    public function destroy(Subscription $subscription): JsonResponse
    {
        $this->authorize('delete', $subscription);

        $this->service->delete($subscription);

        return response()->json(['message' => 'Subscription deleted.']);
    }

    public function export(Request $request, ExportService $export)
    {
        $this->authorize('export', Subscription::class);

        $rows = $this->subscriptions->filtered($request->user(), $request->only(['coaching_id', 'plan_id', 'status']))
            ->latest()
            ->lazy()
            ->map(fn ($s) => [
                $s->id, $s->coaching?->name, $s->plan?->name,
                $s->starts_at->format('d M Y'), $s->ends_at->format('d M Y'),
                $s->amount_paid, ucfirst($s->status), $s->payment_reference,
            ]);

        return $export->download(
            $request->input('format', 'csv'),
            'subscriptions-'.now()->format('Ymd-His'),
            'Subscriptions',
            ['ID', 'Coaching', 'Plan', 'Starts', 'Ends', 'Amount', 'Status', 'Payment Ref'],
            $rows,
        );
    }

    protected function formOptions(Request $request): array
    {
        $user = $request->user();
        $coachings = Coaching::visibleTo($user)->active()->orderBy('name')->get();

        return [
            'coachings'         => $coachings,
            'plans'             => SubscriptionPlan::active()->orderBy('price')->get(),
            'singleCoachingId'  => $coachings->count() === 1 ? $coachings->first()->id : null,
        ];
    }
}
