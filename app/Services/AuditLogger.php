<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditLogger
{
    public static function log(?string $userId, string $module, string $actionType, string $description)
    {
        return AuditLog::create([
            'timestamp' => now(),
            'user_id' => $userId,
            'module' => $module,
            'action_type' => strtoupper($actionType),
            'description' => $description,
        ]);
    }
}
