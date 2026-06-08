<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    protected $fillable = [
        'bus_number',
        'plate_number',
        'capacity',
        'active',
        'current_lat',
        'current_lng',
        'current_speed',
        'current_heading',
        'location_updated_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'current_lat' => 'float',
        'current_lng' => 'float',
        'current_speed' => 'float',
        'current_heading' => 'float',
        'location_updated_at' => 'datetime',
    ];

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(BusLocation::class);
    }
}

