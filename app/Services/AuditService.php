<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;

class AuditService
{
    public function record(
        Organization $organization,
        ?User $actor,
        string $action,
        string $entityType,
        string|int $entityId,
        ?array $before = null,
        ?array $after = null,
        ?Request $request = null,
    ): AuditLog {
        return AuditLog::create([
            'organization_id' => $organization->id,
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => (string) $entityId,
            'before' => $before,
            'after' => $after,
            'occurred_at' => now(),
            'request_id' => $request?->attributes->get('request_id') ?? $request?->header('X-Request-Id'),
        ]);
    }
}
