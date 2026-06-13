<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'parent_id',
        'school_id',
        'full_name',
        'age',
        'grade',
        'school_name',
        'pickup_location',
        'dropoff_location',
        'pickup_time',
        'dropoff_time',
        'has_medical_condition',
        'medical_condition',
        'medication',
        'photo',
        'qr_code',
        'rfid_tag',
        'bus_id',
        'bus_route_id',
        'active',
        'assignment_status',
    ];

    protected $casts = [
        'active' => 'boolean',
        'has_medical_condition' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
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
