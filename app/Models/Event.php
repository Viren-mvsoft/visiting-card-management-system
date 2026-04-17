<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'event_date',
        'location',
    ];

    /**
     * Get the contacts for the event.
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }
}
