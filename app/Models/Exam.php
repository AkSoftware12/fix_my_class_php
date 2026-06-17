<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use BelongsToTenant, SoftDeletes;

    public const TYPES = ['mcq', 'subjective', 'assignment'];

    public const STATUSES = ['draft', 'published', 'completed', 'cancelled'];

    protected $fillable = [
        'coaching_id', 'branch_id', 'school_class_id', 'batch_id', 'subject_id',
        'created_by', 'title', 'type', 'instructions', 'exam_date', 'start_time',
        'duration_minutes', 'total_marks', 'passing_marks', 'status',
    ];

    protected function casts(): array
    {
        return ['exam_date' => 'date'];
    }

    public function coaching(): BelongsTo
    {
        return $this->belongsTo(Coaching::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('sort_order');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }
}
