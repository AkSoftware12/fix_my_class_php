<?php

namespace App\Repositories;

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ChatRoomRepository extends BaseRepository
{
    public function __construct(ChatRoom $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with(['batch', 'creator'])
            ->withCount(['members', 'messages'])
            ->when($filters['search'] ?? null, fn (Builder $q, string $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($filters['type'] ?? null, fn (Builder $q, string $t) => $q->where('type', $t))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
