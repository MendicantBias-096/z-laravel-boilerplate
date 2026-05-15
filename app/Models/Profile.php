<?php

namespace App\Models;

use App\Traits\HasPhoto;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Profile extends Model implements AuditableContract, HasMedia
{
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
