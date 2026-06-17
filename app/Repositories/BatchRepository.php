<?php

namespace App\Repositories;

use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BatchRepository extends BaseRepository
{
    public function __construct(Batch $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with(['branch', 'schoolClass'])
            ->withCount(['students', 'teachers', 'subjects'])
            ->when($filters['search'] ?? null, fn (Builder $q, string $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($filters['branch_id'] ?? null, fn (Builder $q, $id) => $q->where('branch_id', $id))
            ->when($filters['school_class_id'] ?? null, fn (Builder $q, $id) => $q->where('school_class_id', $id))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
