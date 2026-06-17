<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class StudyMaterial extends Model
{
    use BelongsToTenant, SoftDeletes;

    public const FILE_TYPES = ['pdf', 'doc', 'ppt', 'zip', 'image', 'video'];

    protected $fillable = [
        'coaching_id', 'uploaded_by', 'subject_id', 'title', 'description',
        'file_type', 'file_path', 'mime_type', 'size', 'download_count', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function coaching(): BelongsTo
    {
        return $this->belongsTo(Coaching::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(StudyMaterialTarget::class);
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = $bytes > 0 ? min((int) floor(log($bytes, 1024)), count($units) - 1) : 0;

        return round($bytes / (1024 ** $i), 2).' '.$units[$i];
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        if ($user->hasRole('City Admin')) {
            return $query->whereHas('coaching', fn (Builder $q) => $q->where('city_id', $user->city_id));
        }

        $query->where('study_materials.coaching_id', $user->coaching_id);

        if ($user->hasRole('Student')) {
            $student = $user->studentProfile;

            if ($student) {
                $batchIds = collect([$student->batch_id])
                    ->merge($student->batches()->pluck('batches.id'))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                $query->where(function (Builder $q) use ($user, $student, $batchIds) {
                    $q->whereDoesntHave('targets')
                        ->orWhereHas('targets', function (Builder $t) use ($user, $student, $batchIds) {
                            $t->where(fn ($x) => $x->where('target_type', 'coaching')->where('target_id', $user->coaching_id))
                                ->orWhere(fn ($x) => $x->where('target_type', 'branch')->where('target_id', $user->branch_id))
                                ->orWhere(fn ($x) => $x->where('target_type', 'class')->where('target_id', $student->school_class_id))
                                ->orWhere(fn ($x) => $x->where('target_type', 'batch')->whereIn('target_id', $batchIds))
                                ->orWhere(fn ($x) => $x->where('target_type', 'student')->where('target_id', $student->id));
                        });
                });
            }
        }

        return $query;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
