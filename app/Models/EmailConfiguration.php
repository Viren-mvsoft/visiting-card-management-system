<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailConfiguration extends Model
{
    protected $fillable = [
        'name',
        'from_name',
        'from_email',
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'status',
    ];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
