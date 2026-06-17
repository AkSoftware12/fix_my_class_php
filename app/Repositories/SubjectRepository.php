<?php

namespace App\Repositories;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class SubjectRepository extends BaseRepository
{
    public function __construct(Subject $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with('coaching')
            ->when($filters['search'] ?? null, function (Builder $q, string $s) {
                $q->where(fn (Builder $w) => $w
                    ->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%"));
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
