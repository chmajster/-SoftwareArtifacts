<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

final class ActivityLogService
{
    public function __construct(private PDO $db)
    {
    }

    public function log(?int $userId, string $action, string $entityType, ?int $entityId = null, array $meta = []): void
    {
        $stmt = $this->db->prepare('INSERT INTO activity_logs (user_id, action, entity_type, entity_id, meta_json, ip_address) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $userId,
            $action,
            $entityType,
            $entityId,
            json_encode($meta, JSON_THROW_ON_ERROR),
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        ]);
    }
}
