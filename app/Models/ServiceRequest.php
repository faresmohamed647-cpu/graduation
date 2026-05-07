<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $table = 'service_requests';

    protected $fillable = [
        'user_id',
        'role',
        'request_type',
        'subject',
        'description',
        'priority',
        'notes',
        'metadata',
        'status',
        'admin_response',
        'handled_by',
        'handled_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'handled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where('role', strtolower($role));
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
