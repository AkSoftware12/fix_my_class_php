<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineClass extends Model
{
    use BelongsToTenant, SoftDeletes;

    public const STATUSES = ['scheduled', 'live', 'completed', 'cancelled'];

    protected $table = 'online_classes';

    protected $fillable = [
        'coaching_id', 'branch_id', 'batch_id', 'teacher_id', 'title',
        'class_date', 'start_time', 'end_time', 'meeting_link', 'description', 'status',
    ];

    protected function casts(): array
    {
        return ['class_date' => 'date'];
    }

    public function coaching(): BelongsTo
    {
        return $this->belongsTo(Coaching::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        if ($user->hasRole('City Admin')) {
            return $query->whereHas('coaching', fn (Builder $q) => $q->where('city_id', $user->city_id));
        }

        $query->where('online_classes.coaching_id', $user->coaching_id);

        if ($user->hasRole('Student')) {
            $student = $user->studentProfile;

            if ($student) {
                $batchIds = collect([$student->batch_id])
                    ->merge($student->batches()->pluck('batches.id'))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $query->where(fn (Builder $q) => $q->whereNull('batch_id')->orWhereIn('batch_id', $batchIds));
                $query->where(fn (Builder $q) => $q->whereNull('branch_id')->orWhere('branch_id', $user->branch_id));
            }
        }

        return $query;
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')->whereDate('class_date', '>=', today());
    }
}
