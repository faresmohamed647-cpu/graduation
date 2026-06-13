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
        'license_number',
        'license_expiry',
        'license_document_path',
        'insurance_document_path',
        'fleet_type',
        'student_count',
        'bus_count',
        'operating_hours_start',
        'operating_hours_end',
        'commercial_register',
        'profile_submitted_at',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'license_expiry' => 'date',
        'profile_submitted_at' => 'datetime',
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

    protected $appends = [
        'license_document_url',
        'insurance_document_url',
        'logo_url',
    ];

    public function getLicenseDocumentUrlAttribute()
    {
        return $this->license_document_path ? asset('storage/' . $this->license_document_path) : null;
    }

    public function getInsuranceDocumentUrlAttribute()
    {
        return $this->insurance_document_path ? asset('storage/' . $this->insurance_document_path) : null;
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function emergencyAlerts(): HasMany
    {
        return $this->hasMany(EmergencyAlert::class);
    }
}
