<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusRoute extends Model
{
    protected $fillable = [
        'name',
        'type',
        'stops',
        'estimated_minutes',
        'active',
    ];

    protected $casts = [
        'stops' => 'array',
    ];

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}

