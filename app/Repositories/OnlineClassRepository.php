<?php

namespace App\Repositories;

use App\Models\OnlineClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class OnlineClassRepository extends BaseRepository
{
    public function __construct(OnlineClass $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with(['teacher.user', 'batch', 'branch'])
            ->when($user->hasRole('Teacher'), function (Builder $q) use ($user) {
                $q->whereHas('teacher', fn (Builder $t) => $t->where('user_id', $user->id));
            })
            ->when($filters['search'] ?? null, fn (Builder $q, string $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['status'] ?? null, fn (Builder $q, string $s) => $q->where('status', $s))
            ->when($filters['teacher_id'] ?? null, fn (Builder $q, $id) => $q->where('teacher_id', $id))
            ->when($filters['class_date'] ?? null, fn (Builder $q, string $d) => $q->whereDate('class_date', $d));
    }
}
