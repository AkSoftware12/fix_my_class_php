<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionRepository extends BaseRepository
{
    public function __construct(Subscription $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        $query = $this->query()->with(['coaching', 'plan']);

        if (! $user->hasRole('Super Admin')) {
            if ($user->hasRole('City Admin')) {
                $query->whereHas('coaching', fn (Builder $q) => $q->where('city_id', $user->city_id));
            } else {
                $query->where('coaching_id', $user->coaching_id);
            }
        }

        return $query
            ->when($filters['coaching_id'] ?? null, fn (Builder $q, $id) => $q->where('coaching_id', $id))
            ->when($filters['plan_id'] ?? null, fn (Builder $q, $id) => $q->where('subscription_plan_id', $id))
            ->when($filters['status'] ?? null, fn (Builder $q, string $s) => $q->where('status', $s));
    }
}
