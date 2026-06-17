<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class RolePolicy extends BasePolicy
{
    protected string $module = 'roles';

    public function update(User $user, Model $model): bool
    {
        /** @var Role $model */
        // The Super Admin role itself is immutable from the UI.
        if ($model->name === 'Super Admin') {
            return false;
        }

        return parent::update($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        /** @var Role $model */
        if (in_array($model->name, ['Super Admin', 'City Admin', 'Coaching Admin', 'Branch Admin', 'Teacher', 'Student'])) {
            return false;
        }

        return parent::delete($user, $model);
    }
}
