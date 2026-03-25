<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Artifact;
use App\Models\Repository;
use App\Models\Taxonomy;
use App\Models\User;
use App\Services\CsrfService;

final class AdminController extends Controller
{
    public function __construct(array $config, private User $users, private Repository $repositories, private Artifact $artifacts, private Taxonomy $taxonomy)
    {
        parent::__construct($config);
    }

    public function index(): void
    {
        $this->view('admin/index', [
            'users' => $this->users->all(),
            'repositories' => $this->repositories->all(),
            'artifacts' => $this->artifacts->all(),
            'csrf' => CsrfService::token(),
            'taxonomies' => [
                'operating_systems' => $this->taxonomy->all('operating_systems'),
                'architectures' => $this->taxonomy->all('architectures'),
                'package_types' => $this->taxonomy->all('package_types'),
                'release_channels' => $this->taxonomy->all('release_channels'),
            ]
        ]);
    }

    public function addTaxonomy(): void
    {
        if (!CsrfService::validate($_POST['_csrf'] ?? null) || !\App\Services\AuthService::isAdmin()) {
            http_response_code(403);
            exit('Forbidden');
        }

        $table = $_POST['table'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $slug = strtolower(trim($_POST['slug'] ?? ''));

        if ($name && $slug) {
            $this->taxonomy->create($table, $name, $slug);
            flash('success', 'Wartość słownika dodana.');
        }
        $this->redirect('/admin');
    }
}
