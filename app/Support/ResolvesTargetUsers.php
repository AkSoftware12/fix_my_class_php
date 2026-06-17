<?php

namespace App\Support;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Resolves homework/notice/material target rows into the distinct set of
 * student users they address.
 */
trait ResolvesTargetUsers
{
    /**
     * @param  BaseCollection|Collection  $targets  rows with target_type/target_id
     */
    protected function resolveTargetStudentUsers($targets, ?int $coachingId): Collection
    {
        $query = Student::query()->active()->with('user');

        $query->where(function ($outer) use ($targets, $coachingId) {
            $matched = false;

            foreach ($targets as $target) {
                $matched = true;

                $outer->orWhere(function ($q) use ($target) {
                    match ($target->target_type) {
                        'coaching' => $q->where('coaching_id', $target->target_id),
                        'branch' => $q->where('branch_id', $target->target_id),
                        'class' => $q->where('school_class_id', $target->target_id),
                        'batch' => $q->where(fn ($w) => $w
                            ->where('batch_id', $target->target_id)
                            ->orWhereHas('batches', fn ($b) => $b->where('batches.id', $target->target_id))),
                        'student' => $q->where('id', $target->target_id),
                        default => $q->whereRaw('1 = 0'),
                    };
                });
            }

            if (! $matched && $coachingId) {
                $outer->orWhere('coaching_id', $coachingId);
            }
        });

        return $query->get()
            ->pluck('user')
            ->filter(fn (?User $u) => $u && $u->is_active)
            ->unique('id')
            ->values()
            ->pipe(fn ($users) => new Collection($users->all()));
    }
}
