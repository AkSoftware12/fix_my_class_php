<?php

namespace App\Repositories;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class NoticeRepository extends BaseRepository
{
    public function __construct(Notice $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        $query = $this->query()
            ->with(['creator', 'targets'])
            ->withCount('reads');

        if (! $user->hasRole('Super Admin')) {
            $query->where(function (Builder $q) use ($user) {
                $q->where('visibility', 'public');

                if ($user->hasRole('City Admin')) {
                    $q->orWhereHas('coaching', fn (Builder $c) => $c->where('city_id', $user->city_id));
                } elseif ($user->coaching_id) {
                    $q->orWhere('coaching_id', $user->coaching_id);
                }
            });
        }

        return $query
            ->when($filters['search'] ?? null, fn (Builder $q, string $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['type'] ?? null, fn (Builder $q, string $t) => $q->where('type', $t))
            ->when($filters['audience'] ?? null, fn (Builder $q, string $a) => $q->where('audience', $a))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
