<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    public const STATUSES = ['active', 'expired', 'cancelled', 'pending'];

    protected $fillable = [
        'coaching_id', 'subscription_plan_id', 'starts_at', 'ends_at',
        'amount_paid', 'status', 'payment_reference', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function coaching(): BelongsTo
    {
        return $this->belongsTo(Coaching::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->whereDate('ends_at', '>=', today());
    }
}
