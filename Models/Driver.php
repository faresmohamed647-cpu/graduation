<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
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
        'message',
        'status',
        'interview_date',
    ];

    protected function casts(): array
    {
        return [
            'interview_date' => 'datetime',
            'active'         => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}

