<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'school_id',
        'phone',
        'license_number',
        'years_experience',
        'active',
        'state',
        'full_name',
        'age',
        'gender',
        'car_type',
        'car_model',
        'car_plate',
        'address',
        'national_id_path',
        'criminal_record_path',
        'message',
        'status',
        'interview_date',
        'dashboard_sections',
    ];

    public const DEFAULT_DASHBOARD_SECTIONS = [
        'dashboard' => true,
        'today-trip' => true,
        'students' => true,
        'route' => true,
        'trip' => true,
        'notifications' => true,
        'trip-history' => true,
    ];

    protected function casts(): array
    {
        return [
            'interview_date' => 'datetime',
            'active' => 'boolean',
            'dashboard_sections' => 'array',
        ];
    }

    public function resolvedDashboardSections(): array
    {
        $sections = $this->dashboard_sections ?? [];

        return array_merge(self::DEFAULT_DASHBOARD_SECTIONS, is_array($sections) ? $sections : []);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    protected $appends = [
        'national_id_url',
        'criminal_record_url',
    ];

    public function getNationalIdUrlAttribute()
    {
        return $this->national_id_path ? asset('storage/' . $this->national_id_path) : null;
    }

    public function getCriminalRecordUrlAttribute()
    {
        return $this->criminal_record_path ? asset('storage/' . $this->criminal_record_path) : null;
    }

    public function buses(): HasMany
    {
        return $this->hasMany(Bus::class);
    }
}
