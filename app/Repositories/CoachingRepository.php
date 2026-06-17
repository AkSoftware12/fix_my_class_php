<?php

namespace App\Repositories;

use App\Models\Coaching;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CoachingRepository extends BaseRepository
{
    public function __construct(Coaching $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with('city')
            ->withCount(['branches', 'students'])
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(fn (Builder $w) => $w
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%"));
            })
            ->when($filters['city_id'] ?? null, fn (Builder $q, $cityId) => $q->where('city_id', $cityId))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
