<?php

namespace App\Repositories;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BranchRepository extends BaseRepository
{
    public function __construct(Branch $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with('coaching')
            ->withCount(['students', 'teachers'])
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(fn (Builder $w) => $w
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%"));
            })
            ->when($filters['coaching_id'] ?? null, fn (Builder $q, $id) => $q->where('coaching_id', $id))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
