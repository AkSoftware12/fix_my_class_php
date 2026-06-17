<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdmissionLead extends Model
{
    use BelongsToTenant, SoftDeletes;

    public const STAGES = [
        'new' => 'New Lead',
        'follow_up' => 'Follow Up',
        'demo_class' => 'Demo Class',
        'admission_done' => 'Admission Done',
        'rejected' => 'Rejected',
    ];

    protected $fillable = [
        'coaching_id', 'branch_id', 'assigned_to', 'student_name', 'guardian_name',
        'mobile', 'email', 'interested_class', 'source', 'stage',
        'next_follow_up_at', 'notes',
    ];

    protected function casts(): array
    {
        return ['next_follow_up_at' => 'date'];
    }

    public function coaching(): BelongsTo
    {
        return $this->belongsTo(Coaching::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(LeadFollowUp::class)->latest('followed_up_at');
    }

    public function getStageLabelAttribute(): string
    {
        return self::STAGES[$this->stage] ?? ucfirst($this->stage);
    }
}
