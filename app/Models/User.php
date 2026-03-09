<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail, AuditableContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Auditable, HasApiTokens, HasRoles, HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'is_active',
        'is_protected',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_protected'      => 'boolean',
        ];
    }

    /**
     * Impide eliminar (soft delete) a usuarios protegidos.
     */
    public function delete(): ?bool
    {
        if ($this->is_protected) {
            return false;
        }

        return parent::delete();
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Accessor para compatibilidad con partes del sistema que usan $user->name.
     */
    public function notifications(): MorphMany
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->latest();
    }

    public function getNameAttribute(): string
    {
        return trim(($this->profile?->first_name ?? '') . ' ' . ($this->profile?->last_name ?? ''))
            ?: $this->username;
    }
}
