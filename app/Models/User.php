<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'city_id',
        'coaching_id',
        'branch_id',
        'avatar_path',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function coaching(): BelongsTo
    {
        return $this->belongsTo(Coaching::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function teacherProfile(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return Storage::url($this->avatar_path);
        }

        return 'https://ui-avatars.com/api/?background=2563EB&color=fff&name='.urlencode($this->name);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Users visible to the given administrator based on the role hierarchy.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        if ($user->hasRole('City Admin')) {
            return $query
                ->where(function (Builder $q) use ($user) {
                    $q->where('users.city_id', $user->city_id)
                        ->orWhereHas('coaching', fn (Builder $c) => $c->where('city_id', $user->city_id));
                })
                ->where('users.id', '!=', $user->id)
                ->whereDoesntHave('roles', fn (Builder $r) => $r->whereIn('name', ['Super Admin', 'City Admin']));
        }

        $query->where('users.coaching_id', $user->coaching_id);

        if ($user->hasRole('Branch Admin')) {
            $query
                ->where('users.branch_id', $user->branch_id)
                ->whereDoesntHave('roles', fn (Builder $r) => $r->whereIn('name', ['Super Admin', 'City Admin', 'Coaching Admin', 'Branch Admin']));
        }

        return $query->where('users.id', '!=', $user->id);
    }
}
