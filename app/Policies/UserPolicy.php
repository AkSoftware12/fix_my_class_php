<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends BasePolicy
{
    protected string $module = 'users';

    protected function ownsRecord(User $user, Model $model): bool
    {
        /** @var User $target */
        $target = $model;

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Nobody below Super Admin may manage a Super Admin account.
        if ($target->hasRole('Super Admin')) {
            return false;
        }

        if ($user->hasRole('City Admin')) {
            return (int) ($target->city_id ?? $target->coaching?->city_id) === (int) $user->city_id;
        }

        if ((int) $target->coaching_id !== (int) $user->coaching_id) {
            return false;
        }

        if ($user->hasRole('Branch Admin')) {
            return (int) $target->branch_id === (int) $user->branch_id;
        }

        return true;
    }
}
