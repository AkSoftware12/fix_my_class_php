<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Scopes tenant-owned models (coaching_id / branch_id columns) to the
 * hierarchy level of the authenticated user.
 */
trait BelongsToTenant
{
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        if ($user->hasRole('City Admin')) {
            return $query->whereHas('coaching', fn (Builder $q) => $q->where('city_id', $user->city_id));
        }

        if ($user->coaching_id) {
            $query->where($this->getTable().'.coaching_id', $user->coaching_id);
        }

        if ($user->hasRole(['Branch Admin', 'Teacher', 'Student'])
            && $user->branch_id
            && $this->isFillable('branch_id')) {
            $query->where(function (Builder $q) use ($user) {
                $q->where($this->getTable().'.branch_id', $user->branch_id)
                    ->orWhereNull($this->getTable().'.branch_id');
            });
        }

        return $query;
    }
}
