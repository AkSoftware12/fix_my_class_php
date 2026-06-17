<?php

namespace App\Repositories;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SchoolClassRepository extends BaseRepository
{
    public function __construct(SchoolClass $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with(['coaching', 'branch'])
            ->withCount(['batches', 'students'])
            ->when($filters['search'] ?? null, fn (Builder $q, string $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($filters['branch_id'] ?? null, fn (Builder $q, $id) => $q->where('branch_id', $id))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
