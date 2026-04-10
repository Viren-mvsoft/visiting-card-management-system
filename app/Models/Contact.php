<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $fillable = [
        'user_id',
        'country',
        'name',
        'company_name',
        'event',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(ContactPhone::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(ContactEmail::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ContactImage::class);
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function frontImage()
    {
        return $this->images()->where('type', 'front')->first();
    }

    public function backImage()
    {
        return $this->images()->where('type', 'back')->first();
    }

    public function otherImage()
    {
        return $this->images()->where('type', 'other')->first();
    }
}
