<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use PDO;

final class DashboardController extends Controller
{
    public function __construct(array $config, private PDO $db)
    {
        parent::__construct($config);
    }

    public function index(): void
    {
        $userId = (int)($_SESSION['user']['id'] ?? 0);

        $stats = [
            'repositories' => (int)$this->db->query("SELECT COUNT(*) FROM repositories WHERE owner_id = {$userId}")->fetchColumn(),
            'artifacts' => (int)$this->db->query("SELECT COUNT(*) FROM artifacts WHERE uploader_id = {$userId}")->fetchColumn(),
            'downloads' => (int)$this->db->query("SELECT COALESCE(SUM(download_count),0) FROM artifacts WHERE uploader_id = {$userId}")->fetchColumn(),
        ];

        $stmt = $this->db->prepare('SELECT action, entity_type, created_at FROM activity_logs WHERE user_id = ? ORDER BY id DESC LIMIT 10');
        $stmt->execute([$userId]);

        $this->view('dashboard/index', [
            'stats' => $stats,
            'activities' => $stmt->fetchAll(),
        ]);
    }
}
