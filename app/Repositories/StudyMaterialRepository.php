<?php

namespace App\Repositories;

use App\Models\StudyMaterial;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class StudyMaterialRepository extends BaseRepository
{
    public function __construct(StudyMaterial $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with(['uploader', 'subject', 'targets'])
            ->when($filters['search'] ?? null, fn (Builder $q, string $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($filters['file_type'] ?? null, fn (Builder $q, string $t) => $q->where('file_type', $t))
            ->when($filters['subject_id'] ?? null, fn (Builder $q, $id) => $q->where('subject_id', $id))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }
}
