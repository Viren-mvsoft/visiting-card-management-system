<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactEmail extends Model
{
    protected $fillable = ['contact_id', 'email', 'label'];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
