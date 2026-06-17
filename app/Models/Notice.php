<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Notice extends Model
{
    use BelongsToTenant, SoftDeletes;

    public const TYPES = ['general', 'holiday', 'event', 'exam', 'urgent'];

    public const AUDIENCES = ['all', 'teachers', 'students'];

    protected $fillable = [
        'coaching_id', 'created_by', 'title', 'body', 'type', 'visibility',
        'audience', 'attachment_path', 'publish_at', 'expires_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function coaching(): BelongsTo
    {
        return $this->belongsTo(Coaching::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targets(): HasMany
    {
        return $this->hasMany(NoticeTarget::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(NoticeRead::class);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? Storage::url($this->attachment_path) : null;
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        if ($user->hasRole('City Admin')) {
            return $query->whereHas('coaching', fn (Builder $q) => $q->where('city_id', $user->city_id));
        }

        $query->where('notices.coaching_id', $user->coaching_id);

        if ($user->hasRole('Student')) {
            $student = $user->studentProfile;

            $query->whereIn('audience', ['all', 'students']);
            $query->where(function (Builder $q) use ($user, $student) {
                $q->where('visibility', 'public');

                if ($student) {
                    $batchIds = collect([$student->batch_id])
                        ->merge($student->batches()->pluck('batches.id'))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $q->orWhere(function (Builder $inner) use ($user, $student, $batchIds) {
                        $inner->where('visibility', 'private')
                            ->whereHas('targets', function (Builder $t) use ($user, $student, $batchIds) {
                                $t->where(fn ($x) => $x->where('target_type', 'coaching')->where('target_id', $user->coaching_id))
                                    ->orWhere(fn ($x) => $x->where('target_type', 'branch')->where('target_id', $user->branch_id))
                                    ->orWhere(fn ($x) => $x->where('target_type', 'class')->where('target_id', $student->school_class_id))
                                    ->orWhere(fn ($x) => $x->where('target_type', 'batch')->whereIn('target_id', $batchIds))
                                    ->orWhere(fn ($x) => $x->where('target_type', 'student')->where('target_id', $student->id));
                            });
                    });
                }
            });
        } elseif ($user->hasRole('Teacher')) {
            $query->whereIn('audience', ['all', 'teachers']);
        }

        return $query;
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn (Builder $q) => $q->whereNull('publish_at')->orWhere('publish_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhereDate('expires_at', '>=', today()));
    }
}
