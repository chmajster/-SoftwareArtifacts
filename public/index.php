<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\ArtifactController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PublicController;
use App\Controllers\RepositoryController;
use App\Core\Database;
use App\Core\Router;
use App\Models\Artifact;
use App\Models\Repository;
use App\Models\Taxonomy;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AuthService;
use App\Services\UploadService;

$config = require __DIR__ . '/../app/bootstrap.php';
require __DIR__ . '/../app/Helpers/helpers.php';

if (!($config['app']['installed'] ?? false) || !is_file(__DIR__ . '/../storage/installed.lock')) {
    header('Location: /install/');
    exit;
}

$db = Database::getConnection($config);
$router = new Router();

$userModel = new User($db);
$repositoryModel = new Repository($db);
$artifactModel = new Artifact($db);
$taxonomyModel = new Taxonomy($db);
$activity = new ActivityLogService($db);
$auth = new AuthService($userModel);
$upload = new UploadService($config);

$authController = new AuthController($config, $userModel, $auth, $activity);
$dashboardController = new DashboardController($config, $db);
$repositoryController = new RepositoryController($config, $repositoryModel, $taxonomyModel, $activity);
$artifactController = new ArtifactController($config, $artifactModel, $repositoryModel, $taxonomyModel, $upload, $activity);
$publicController = new PublicController($config, $repositoryModel, $artifactModel);
$adminController = new AdminController($config, $userModel, $repositoryModel, $artifactModel, $taxonomyModel);

$router->add('GET', '/', static fn() => $publicController->listRepositories());
$router->add('GET', '/login', static fn() => $authController->showLogin());
$router->add('POST', '/login', static fn() => $authController->login());
$router->add('GET', '/register', static fn() => $authController->showRegister());
$router->add('POST', '/register', static fn() => $authController->register());
$router->add('GET', '/reset-password', static fn() => $authController->showReset());
$router->add('POST', '/logout', static fn() => $authController->logout());

$requiresAuth = ['/dashboard', '/repositories', '/repositories/create', '/artifacts', '/artifacts/upload', '/admin'];
$path = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/', '/') ?: '/';
if (in_array($path, $requiresAuth, true) && !\App\Services\AuthService::check()) {
    header('Location: /login');
    exit;
}
if ($path === '/admin' && !\App\Services\AuthService::isAdmin()) {
    http_response_code(403);
    exit('Forbidden');
}

$router->add('GET', '/dashboard', static fn() => $dashboardController->index());
$router->add('GET', '/repositories', static fn() => $repositoryController->index());
$router->add('GET', '/repositories/create', static fn() => $repositoryController->createForm());
$router->add('POST', '/repositories/create', static fn() => $repositoryController->create());

$router->add('GET', '/artifacts', static fn() => $artifactController->index());
$router->add('GET', '/artifacts/upload', static fn() => $artifactController->uploadForm());
$router->add('POST', '/artifacts/upload', static fn() => $artifactController->upload());
$router->add('GET', '/artifacts/download', static fn() => $artifactController->download());

$router->add('GET', '/r', static fn() => $publicController->showRepository());
$router->add('GET', '/admin', static fn() => $adminController->index());
$router->add('POST', '/admin/taxonomies', static fn() => $adminController->addTaxonomy());

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
