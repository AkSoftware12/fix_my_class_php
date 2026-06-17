<?php

namespace App\Repositories;

use App\Models\AdmissionLead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class AdmissionLeadRepository extends BaseRepository
{
    public function __construct(AdmissionLead $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with(['branch', 'assignee'])
            ->when($filters['search'] ?? null, function (Builder $q, string $s) {
                $q->where(fn (Builder $w) => $w
                    ->where('student_name', 'like', "%{$s}%")
                    ->orWhere('mobile', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%"));
            })
            ->when($filters['stage'] ?? null, fn (Builder $q, string $s) => $q->where('stage', $s))
            ->when($filters['branch_id'] ?? null, fn (Builder $q, $id) => $q->where('branch_id', $id))
            ->when($filters['assigned_to'] ?? null, fn (Builder $q, $id) => $q->where('assigned_to', $id));
    }

    public function stageCounts(User $user): array
    {
        return $this->query()
            ->visibleTo($user)
            ->selectRaw('stage, count(*) as total')
            ->groupBy('stage')
            ->pluck('total', 'stage')
            ->all();
    }
}
