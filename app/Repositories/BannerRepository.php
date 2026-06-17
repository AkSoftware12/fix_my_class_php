<?php

namespace App\Repositories;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class BannerRepository extends BaseRepository
{
    public function __construct(Banner $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->with('coaching')
            ->visibleTo($user)
            ->when($filters['search'] ?? null, fn (Builder $q, string $s) =>
                $q->where('title', 'like', "%{$s}%"))
            ->when($filters['coaching_id'] ?? null, fn (Builder $q, $id) =>
                $q->where('coaching_id', $id))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) =>
                $q->where('is_active', (bool) $filters['status']))
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }
}
