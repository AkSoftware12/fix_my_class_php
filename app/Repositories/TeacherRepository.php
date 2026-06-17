<?php

namespace App\Repositories;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class TeacherRepository extends BaseRepository
{
    public function __construct(Teacher $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with(['user', 'branch', 'subject'])
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->whereHas('user', fn (Builder $u) => $u
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%"));
            })
            ->when($filters['branch_id'] ?? null, fn (Builder $q, $id) => $q->where('branch_id', $id))
            ->when($filters['subject_id'] ?? null, fn (Builder $q, $id) => $q->where('subject_id', $id))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
