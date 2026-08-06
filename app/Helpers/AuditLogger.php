<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditLogger
{
    public static function log($eventName, $description, $context = 'Sistema', $component = 'Sistema')
    {
        $user = auth()->user();

        AuditLog::create([
            'user_id' => $user ? $user->id : null,
            'user_name' => $user ? $user->name : 'Invitado / Sistema',
            'event_context' => $context,
            'component' => $component,
            'event_name' => $eventName,
            'description' => $description,
            'origin' => 'web',
            'ip_address' => request()->ip(),
        ]);
    }
}