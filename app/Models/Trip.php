<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $fillable = [
        'school_id',
        'driver_id',
        'bus_id',
        'bus_route_id',
        'trip_date',
        'shift',
        'status',
        'started_at',
        'ended_at',
        'meta',
    ];

    protected $casts = [
        'trip_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'meta' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(BusRoute::class, 'bus_route_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}

