<?php

namespace App\Modules\Access\Models;

use App\Modules\Access\Database\Factories\ProfileFactory;
use App\Modules\Access\Traits\HasPhoto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Profile extends Model implements AuditableContract, HasMedia
{
    protected $table = 'access_profiles';

    /**
     * El resolver de factories de Eloquent busca `Database\Factories\…` a
     * partir de `App\`, así que para un modelo de módulo apunta a una clase
     * que no existe. `Factory::guessFactoryNamesUsing()` es un static global y
     * no admite una versión por módulo, así que cada modelo lo declara (R6).
     */
    protected static function newFactory(): ProfileFactory
    {
        return ProfileFactory::new();
    }

    /** @use HasFactory<ProfileFactory> */
    use Auditable, HasFactory, HasPhoto, InteractsWithMedia {
        HasPhoto::registerMediaCollections insteadof InteractsWithMedia;
        HasPhoto::registerMediaConversions insteadof InteractsWithMedia;
    }

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'locale',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
