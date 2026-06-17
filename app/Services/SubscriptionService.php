<?php

namespace App\Services;

use App\Models\Subscription;
use App\Repositories\SubscriptionRepository;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function __construct(protected SubscriptionRepository $subscriptions)
    {
    }

    public function create(array $data): Subscription
    {
        return DB::transaction(function () use ($data) {
            if (($data['status'] ?? 'active') === 'active') {
                // A coaching can hold only one active subscription at a time.
                Subscription::where('coaching_id', $data['coaching_id'])
                    ->where('status', 'active')
                    ->update(['status' => 'expired']);
            }

            return $this->subscriptions->create(collect($data)->only([
                'coaching_id', 'subscription_plan_id', 'starts_at', 'ends_at',
                'amount_paid', 'status', 'payment_reference', 'notes',
            ])->all());
        });
    }

    public function update(Subscription $subscription, array $data): Subscription
    {
        return $this->subscriptions->update($subscription, collect($data)->only([
            'subscription_plan_id', 'starts_at', 'ends_at',
            'amount_paid', 'status', 'payment_reference', 'notes',
        ])->all());
    }

    public function cancel(Subscription $subscription): Subscription
    {
        $subscription->update(['status' => 'cancelled']);

        return $subscription;
    }

    public function delete(Subscription $subscription): void
    {
        $this->subscriptions->delete($subscription);
    }
}
