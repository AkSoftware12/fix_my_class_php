<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadFollowUp extends Model
{
    protected $fillable = [
        'admission_lead_id', 'user_id', 'stage', 'remarks', 'followed_up_at',
    ];

    protected function casts(): array
    {
        return ['followed_up_at' => 'datetime'];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(AdmissionLead::class, 'admission_lead_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
