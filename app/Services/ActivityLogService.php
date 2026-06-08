<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function log(
        Request $request,
        string $action,
        ?Model $entity = null,
        array $meta = [],
    ): ActivityLog {
        $user = $request->user();

        return ActivityLog::create([
            'school_id' => $user?->school_id,
            'user_id' => $user?->id,
            'action' => $action,
            'entity_type' => $entity ? $entity->getMorphClass() : null,
            'entity_id' => $entity?->getKey(),
            'meta' => $meta ?: null,
            'ip_address' => $request->ip(),
        ]);
    }
}
