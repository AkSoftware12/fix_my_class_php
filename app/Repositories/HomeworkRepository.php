<?php

namespace App\Repositories;

use App\Models\Homework;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class HomeworkRepository extends BaseRepository
{
    public function __construct(Homework $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with(['creator', 'subject', 'targets'])
            ->withCount('submissions')
            ->when($user->hasRole('Teacher'), fn (Builder $q) => $q->where('created_by', $user->id))
            ->when($filters['search'] ?? null, fn (Builder $q, string $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['type'] ?? null, fn (Builder $q, string $t) => $q->where('type', $t))
            ->when($filters['status'] ?? null, fn (Builder $q, string $s) => $q->where('status', $s))
            ->when($filters['subject_id'] ?? null, fn (Builder $q, $id) => $q->where('subject_id', $id))
            ->when($filters['due_date'] ?? null, fn (Builder $q, string $d) => $q->whereDate('due_date', $d));
    }
}
