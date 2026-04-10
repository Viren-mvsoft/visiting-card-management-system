<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactImage extends Model
{
    protected $fillable = ['contact_id', 'type', 'file_path', 'file_name'];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
