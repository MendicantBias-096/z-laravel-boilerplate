<?php

namespace App\Modules\Access\Models;

use App\Modules\Access\Database\Factories\UserFactory;
use App\Modules\Platform\Models\DatabaseNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\PasskeyAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Permission\Traits\HasRoles;

/**
 * PHPStan no conoce las columnas de Eloquent: sin esto, cada lectura desde una
 * Policy o una vista es un error de propiedad indefinida.
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property bool $is_active
 * @property bool $is_protected
 */
class User extends Authenticatable implements AuditableContract, MustVerifyEmail, PasskeyUser
{
    /**
     * El resolver de factories de Eloquent busca `Database\Factories\…` a
     * partir de `App\`, así que para un modelo de módulo apunta a una clase
     * que no existe. `Factory::guessFactoryNamesUsing()` es un static global y
     * no admite una versión por módulo, así que cada modelo lo declara (R6).
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /** @use HasFactory<UserFactory> */
    use Auditable, HasApiTokens, HasFactory, HasRoles, Notifiable, PasskeyAuthenticatable, SoftDeletes, TwoFactorAuthenticatable;

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

    #[\Override]
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_protected' => 'boolean',
        ];
    }

    /**
     * Impide eliminar (soft delete) a usuarios protegidos.
     */
    #[\Override]
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
        return trim(($this->profile?->first_name ?? '').' '.($this->profile?->last_name ?? ''))
            ?: $this->username;
    }
}
