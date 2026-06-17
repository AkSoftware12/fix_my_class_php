<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Permission-driven policy. Child classes set $module (permission prefix)
 * and may override ownsRecord() for tenant ownership checks.
 */
abstract class BasePolicy
{
    protected string $module;

    public function viewAny(User $user): bool
    {
        return $user->can("{$this->module}.view");
    }

    public function view(User $user, Model $model): bool
    {
        return $user->can("{$this->module}.view") && $this->ownsRecord($user, $model);
    }

    public function create(User $user): bool
    {
        return $user->can("{$this->module}.create");
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can("{$this->module}.edit") && $this->ownsRecord($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can("{$this->module}.delete") && $this->ownsRecord($user, $model);
    }

    public function export(User $user): bool
    {
        return $user->can("{$this->module}.export");
    }

    public function assign(User $user, ?Model $model = null): bool
    {
        return $user->can("{$this->module}.assign")
            && ($model === null || $this->ownsRecord($user, $model));
    }

    /**
     * Tenant ownership: Super Admin sees everything; City Admin records in
     * their city; everyone else records of their own coaching (and branch
     * when the record carries one and the user is branch-scoped).
     */
    protected function ownsRecord(User $user, Model $model): bool
    {
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        $coachingId = $model->getAttribute('coaching_id');

        if ($coachingId === null) {
            return true;
        }

        if ($user->hasRole('City Admin')) {
            return \App\Models\Coaching::where('id', $coachingId)
                ->where('city_id', $user->city_id)
                ->exists();
        }

        if ((int) $coachingId !== (int) $user->coaching_id) {
            return false;
        }

        $branchId = $model->getAttribute('branch_id');

        if ($branchId !== null && $user->hasRole(['Branch Admin', 'Teacher'])) {
            return (int) $branchId === (int) $user->branch_id;
        }

        return true;
    }
}
