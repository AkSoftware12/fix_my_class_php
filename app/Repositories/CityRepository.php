<?php

namespace App\Repositories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class CityRepository extends BaseRepository
{
    public function __construct(City $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->withCount(['coachings'])
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(fn (Builder $w) => $w
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%"));
            })
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
