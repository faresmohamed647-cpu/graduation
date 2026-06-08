<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusRoute extends Model
{
    protected $fillable = [
        'school_id',
        'bus_id',
        'driver_id',
        'name',
        'type',
        'stops',
        'estimated_minutes',
        'distance_km',
        'active',
    ];

    protected $casts = [
        'stops' => 'array',
        'distance_km' => 'float',
        'active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'bus_route_id');
    }
}
