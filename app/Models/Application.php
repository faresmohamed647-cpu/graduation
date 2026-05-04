<?php

namespace App\Models;

use App\Enums\ApplicationRole;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';

    public const ROLE_PARENT = ApplicationRole::Parent->value;
    public const ROLE_DRIVER = ApplicationRole::Driver->value;
    public const ROLE_ADMIN  = ApplicationRole::Admin->value;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'address',
        'role',
        'experience',
        'notes',
        'status',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function allowedRoles(): array
    {
        return ApplicationRole::values();
    }

    /**
     * Parse and return role-specific metadata embedded in notes.
     */
    public function getMetadataAttribute(): array
    {
        if ($this->notes && str_contains($this->notes, 'meta:')) {
            $parts = explode('meta:', $this->notes, 2);
            return json_decode($parts[1] ?? '{}', true) ?: [];
        }
        return [];
    }

    /**
     * Return the clean notes text without the embedded meta JSON.
     */
    public function getCleanNotesAttribute(): ?string
    {
        if ($this->notes && str_contains($this->notes, 'meta:')) {
            return trim(explode('meta:', $this->notes, 2)[0]) ?: null;
        }
        return $this->notes;
    }
}
