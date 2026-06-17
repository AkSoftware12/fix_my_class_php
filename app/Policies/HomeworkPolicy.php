<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class HomeworkPolicy extends BasePolicy
{
    protected string $module = 'homework';

    protected function ownsRecord(User $user, Model $model): bool
    {
        if (! parent::ownsRecord($user, $model)) {
            return false;
        }

        // Teachers may only manage homework they created.
        if ($user->hasRole('Teacher')) {
            return (int) $model->getAttribute('created_by') === (int) $user->id;
        }

        return true;
    }
}
