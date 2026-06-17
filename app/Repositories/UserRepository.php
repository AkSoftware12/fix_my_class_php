<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->whereDoesntHave('roles', fn (Builder $q) => $q->whereIn('name', ['Student', 'Coaching Admin']))
            ->with(['roles', 'coaching', 'branch'])
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(fn (Builder $w) => $w
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%"));
            })
            ->when($filters['role'] ?? null, fn (Builder $q, string $role) => $q->role($role))
            ->when($filters['coaching_id'] ?? null, fn (Builder $q, $id) => $q->where('coaching_id', $id))
            ->when($filters['branch_id'] ?? null, fn (Builder $q, $id) => $q->where('branch_id', $id))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
