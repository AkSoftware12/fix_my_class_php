<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeworkTarget extends Model
{
    protected $fillable = ['homework_id', 'target_type', 'target_id'];

    public function homework(): BelongsTo
    {
        return $this->belongsTo(Homework::class);
    }

    public function getTargetNameAttribute(): string
    {
        $model = match ($this->target_type) {
            'coaching' => Coaching::find($this->target_id)?->name,
            'branch' => Branch::find($this->target_id)?->name,
            'class' => SchoolClass::find($this->target_id)?->name,
            'batch' => Batch::find($this->target_id)?->name,
            'student' => Student::with('user')->find($this->target_id)?->user?->name,
            default => null,
        };

        return $model ?? ucfirst($this->target_type).' #'.$this->target_id;
    }
}
