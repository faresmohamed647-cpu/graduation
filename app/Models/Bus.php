<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bus extends Model
{
    protected $fillable = [
        'school_id',
        'driver_id',
        'bus_route_id',
        'bus_number',
        'plate_number',
        'capacity',
        'insurance_expiry',
        'documents',
        'status',
        'active',
        'current_lat',
        'current_lng',
        'current_speed',
        'current_heading',
        'location_updated_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'documents' => 'array',
        'insurance_expiry' => 'date',
        'current_lat' => 'float',
        'current_lng' => 'float',
        'current_speed' => 'float',
        'current_heading' => 'float',
        'location_updated_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(BusRoute::class, 'bus_route_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(BusLocation::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
