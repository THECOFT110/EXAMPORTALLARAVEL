<?php

namespace App\Services;

use App\Models\AuditLog;

class AuditService
{
    /**
     * Log an action
     */
    public function log(
        ?string $userId,
        string $action,
        ?string $entity = null,
        ?string $entityId = null,
        ?string $details = null,
        ?string $ipAddress = null
    ): void {
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity' => $entity,
            'entity_id' => $entityId,
            'details' => $details,
            'ip_address' => $ipAddress ?? request()->ip(),
        ]);
    }

    /**
     * Get user activity
     */
    public function getUserActivity(string $userId, int $limit = 50): array
    {
        return AuditLog::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($log) => [
                'action' => $log->action,
                'entity' => $log->entity,
                'details' => $log->details,
                'created_at' => $log->created_at,
            ])
            ->toArray();
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity(int $hours = 24, int $limit = 100): array
    {
        return AuditLog::with('user')
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($log) => [
                'user_name' => $log->user?->full_name ?? 'System',
                'action' => $log->action,
                'entity' => $log->entity,
                'details' => $log->details,
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at,
            ])
            ->toArray();
    }

    /**
     * Get activity by action type
     */
    public function getActivityByAction(string $action, int $limit = 50): array
    {
        return AuditLog::with('user')
            ->where('action', $action)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
