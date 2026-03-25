<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Artifact;
use App\Models\Repository;

final class PublicController extends Controller
{
    public function __construct(array $config, private Repository $repositories, private Artifact $artifacts)
    {
        parent::__construct($config);
    }

    public function listRepositories(): void
    {
        $this->view('public/repositories', ['repositories' => $this->repositories->allPublic()], 'layouts/guest');
    }

    public function showRepository(): void
    {
        $slug = trim($_GET['slug'] ?? '');
        $repository = $this->repositories->findBySlug($slug);

        if (!$repository || $repository['visibility'] !== 'public') {
            http_response_code(404);
            echo 'Public repository not found';
            return;
        }

        $artifacts = $this->artifacts->byRepository((int)$repository['id'], true);

        $this->view('public/repository', [
            'repository' => $repository,
            'artifacts' => $artifacts,
            'metadataUrl' => '/public/metadata.php?slug=' . urlencode($slug),
        ], 'layouts/guest');
    }
}
