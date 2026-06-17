<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class BranchPolicy extends BasePolicy
{
    protected string $module = 'branches';

    protected function ownsRecord(User $user, Model $model): bool
    {
        if (! parent::ownsRecord($user, $model)) {
            return false;
        }

        if ($user->hasRole('Branch Admin')) {
            return (int) $model->getKey() === (int) $user->branch_id;
        }

        return true;
    }
}
