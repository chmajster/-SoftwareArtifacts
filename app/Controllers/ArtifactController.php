<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Artifact;
use App\Models\Repository;
use App\Models\Taxonomy;
use App\Services\ActivityLogService;
use App\Services\CsrfService;
use App\Services\UploadService;

final class ArtifactController extends Controller
{
    public function __construct(array $config, private Artifact $artifacts, private Repository $repositories, private Taxonomy $taxonomy, private UploadService $upload, private ActivityLogService $activity)
    {
        parent::__construct($config);
    }

    public function index(): void
    {
        $repoId = (int)($_GET['repository_id'] ?? 0);
        $items = $repoId > 0 ? $this->artifacts->byRepository($repoId) : [];
        $repos = $this->repositories->byOwner((int)$_SESSION['user']['id']);

        $this->view('artifacts/index', ['artifacts' => $items, 'repositories' => $repos, 'selectedRepoId' => $repoId]);
    }

    public function uploadForm(): void
    {
        $this->view('artifacts/upload', [
            'csrf' => CsrfService::token(),
            'repositories' => $this->repositories->byOwner((int)$_SESSION['user']['id']),
            'oss' => $this->taxonomy->all('operating_systems'),
            'archs' => $this->taxonomy->all('architectures'),
            'packageTypes' => $this->taxonomy->all('package_types'),
        ]);
    }

    public function upload(): void
    {
        if (!CsrfService::validate($_POST['_csrf'] ?? null)) {
            flash('error', 'Invalid CSRF token.');
            $this->redirect('/artifacts/upload');
        }

        try {
            $file = $this->upload->store($_FILES['artifact'] ?? []);
            $artifactId = $this->artifacts->create([
                'repository_id' => (int)$_POST['repository_id'],
                'uploader_id' => (int)$_SESSION['user']['id'],
                'name' => trim($_POST['name']),
                'version' => trim($_POST['version']),
                'description' => trim($_POST['description'] ?? ''),
                'os_id' => (int)$_POST['os_id'],
                'architecture_id' => (int)$_POST['architecture_id'],
                'package_type_id' => (int)$_POST['package_type_id'],
                'changelog' => trim($_POST['changelog'] ?? ''),
                'status' => $_POST['status'] ?? 'draft',
                'is_latest' => isset($_POST['is_latest']) ? 1 : 0,
                'is_hidden' => isset($_POST['is_hidden']) ? 1 : 0,
                'is_deprecated' => isset($_POST['is_deprecated']) ? 1 : 0,
                'file_original_name' => $file['original_name'],
                'file_stored_name' => $file['stored_name'],
                'file_size_bytes' => $file['size_bytes'],
                'file_mime_type' => $file['mime_type'],
                'checksum_sha256' => $file['checksum_sha256'],
                'published_at' => date('Y-m-d H:i:s'),
            ]);
            $this->activity->log((int)$_SESSION['user']['id'], 'artifact_uploaded', 'artifact', $artifactId);
            flash('success', 'Artifact uploaded.');
        } catch (\Throwable $e) {
            flash('error', $e->getMessage());
        }

        $this->redirect('/artifacts');
    }

    public function download(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $artifact = $this->artifacts->find($id);

        if (!$artifact) {
            http_response_code(404);
            echo 'Artifact not found';
            return;
        }

        $path = rtrim($this->config['storage']['artifacts_path'], '/') . '/' . $artifact['file_stored_name'];
        if (!is_file($path)) {
            http_response_code(404);
            echo 'File not found';
            return;
        }

        $this->artifacts->incrementDownloads($id);
        header('Content-Type: ' . $artifact['file_mime_type']);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: attachment; filename="' . basename($artifact['file_original_name']) . '"');
        readfile($path);
        exit;
    }
}
