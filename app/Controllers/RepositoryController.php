<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Repository;
use App\Models\Taxonomy;
use App\Services\ActivityLogService;
use App\Services\CsrfService;

final class RepositoryController extends Controller
{
    public function __construct(array $config, private Repository $repositories, private Taxonomy $taxonomy, private ActivityLogService $activity)
    {
        parent::__construct($config);
    }

    public function index(): void
    {
        $items = $this->repositories->byOwner((int)$_SESSION['user']['id']);
        $this->view('repositories/index', ['repositories' => $items]);
    }

    public function createForm(): void
    {
        $this->view('repositories/create', [
            'csrf' => CsrfService::token(),
            'oss' => $this->taxonomy->all('operating_systems'),
            'archs' => $this->taxonomy->all('architectures'),
            'packageTypes' => $this->taxonomy->all('package_types'),
            'channels' => $this->taxonomy->all('release_channels'),
        ]);
    }

    public function create(): void
    {
        if (!CsrfService::validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            $this->redirect('/repositories/create');
        }

        $name = trim($_POST['name'] ?? '');
        $slug = strtolower(trim($_POST['slug'] ?? ''));

        if ($name === '' || $slug === '' || !preg_match('/^[a-z0-9-]+$/', $slug)) {
            flash('error', 'Podaj poprawną nazwę i slug (a-z0-9-).');
            $this->redirect('/repositories/create');
        }

        $repoId = $this->repositories->create([
            'owner_id' => (int)$_SESSION['user']['id'],
            'name' => $name,
            'slug' => $slug,
            'description' => trim($_POST['description'] ?? ''),
            'visibility' => in_array($_POST['visibility'] ?? 'private', ['public', 'private'], true) ? $_POST['visibility'] : 'private',
            'os_id' => (int)($_POST['os_id'] ?? 1),
            'architecture_id' => (int)($_POST['architecture_id'] ?? 1),
            'package_type_id' => (int)($_POST['package_type_id'] ?? 1),
            'release_channel_id' => (int)($_POST['release_channel_id'] ?? 1),
            'version' => trim($_POST['version'] ?? '1.0.0'),
            'listing_enabled' => isset($_POST['listing_enabled']) ? 1 : 0,
            'is_custom' => isset($_POST['is_custom']) ? 1 : 0,
        ]);

        $this->activity->log((int)$_SESSION['user']['id'], 'repository_created', 'repository', $repoId);
        flash('success', 'Repozytorium utworzone.');
        $this->redirect('/repositories');
    }
}
