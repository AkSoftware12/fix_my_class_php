<?php

namespace App\Repositories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class StudentRepository extends BaseRepository
{
    public function __construct(Student $model)
    {
        parent::__construct($model);
    }

    public function filtered(User $user, array $filters = []): Builder
    {
        return $this->query()
            ->visibleTo($user)
            ->with(['user', 'branch', 'schoolClass', 'batch'])
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $w) use ($search) {
                    $w->where('admission_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn (Builder $u) => $u
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%"));
                });
            })
            ->when($filters['branch_id'] ?? null, fn (Builder $q, $id) => $q->where('branch_id', $id))
            ->when($filters['school_class_id'] ?? null, fn (Builder $q, $id) => $q->where('school_class_id', $id))
            ->when($filters['batch_id'] ?? null, fn (Builder $q, $id) => $q->where('batch_id', $id))
            ->when(isset($filters['status']) && $filters['status'] !== '', fn (Builder $q) => $q->where('is_active', (bool) $filters['status']));
    }

    public function nextAdmissionNumber(int $coachingId): string
    {
        $sequence = $this->model->newQueryWithoutScopes()
            ->where('coaching_id', $coachingId)
            ->count() + 1;

        return sprintf('ADM-%d-%05d', $coachingId, $sequence);
    }
}
