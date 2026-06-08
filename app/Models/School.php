<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'notes',
        'logo',
        'principal_name',
        'status',
    ];

    public function administrators(): HasMany
    {
        return $this->hasMany(User::class)->where('role', 'school_admin');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(BusRoute::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function emergencyAlerts(): HasMany
    {
        return $this->hasMany(EmergencyAlert::class);
    }
}
