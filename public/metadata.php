<?php

declare(strict_types=1);

use App\Core\Database;
use App\Models\Artifact;
use App\Models\Repository;

$config = require __DIR__ . '/../app/bootstrap.php';

$db = Database::getConnection($config);
$repoModel = new Repository($db);
$artifactModel = new Artifact($db);

$slug = trim($_GET['slug'] ?? '');
$repo = $repoModel->findBySlug($slug);

if (!$repo || $repo['visibility'] !== 'public') {
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Repository not found']);
    exit;
}

$artifacts = $artifactModel->byRepository((int)$repo['id'], true);

header('Content-Type: application/json');
echo json_encode([
    'repository' => [
        'name' => $repo['name'],
        'slug' => $repo['slug'],
        'version' => $repo['version'],
        'release_channel_id' => $repo['release_channel_id'],
    ],
    'artifacts' => $artifacts,
], JSON_PRETTY_PRINT);
