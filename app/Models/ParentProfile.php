<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentProfile extends Model
{
    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'active',
        'status',
        'profile_approved_at',
        'state',
        'relationship',
        'student_count',
        'degree',
        'education_system',
        'school_name',
        'school_address',
        'school_starting',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'profile_approved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_id');
    }
}

