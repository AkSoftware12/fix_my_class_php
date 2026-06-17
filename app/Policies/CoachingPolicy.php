<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CoachingPolicy extends BasePolicy
{
    protected string $module = 'coachings';

    protected function ownsRecord(User $user, Model $model): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if ($user->hasRole('City Admin')) {
            return (int) $model->getAttribute('city_id') === (int) $user->city_id;
        }

        return (int) $model->getKey() === (int) $user->coaching_id;
    }
}
