<?php

declare(strict_types=1);

$lockFile = __DIR__ . '/../storage/installed.lock';
$configTarget = __DIR__ . '/../config/config.local.php';

if (is_file($lockFile)) {
    echo '<h2>Instalacja już wykonana</h2><p>Usuń storage/installed.lock aby uruchomić instalator ponownie.</p>';
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost = trim($_POST['db_host'] ?? '127.0.0.1');
    $dbPort = (int)($_POST['db_port'] ?? 3306);
    $dbName = trim($_POST['db_name'] ?? 'artifacts_hub');
    $dbUser = trim($_POST['db_user'] ?? 'root');
    $dbPass = (string)($_POST['db_pass'] ?? '');
    $baseUrl = trim($_POST['base_url'] ?? 'http://localhost:8000');

    if (version_compare(PHP_VERSION, '8.0.0', '<')) {
        $errors[] = 'PHP 8.0+ jest wymagane.';
    }

    try {
        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $dbHost, $dbPort, $dbName),
            $dbUser,
            $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        $schemaSql = file_get_contents(__DIR__ . '/sql/schema.sql');
        $seedSql = file_get_contents(__DIR__ . '/sql/seed.sql');

        $pdo->exec($schemaSql);
        $pdo->exec($seedSql);

        $hash = password_hash('admin', PASSWORD_DEFAULT);
        $pdo->prepare('INSERT INTO users (username, email, password_hash, role_id) VALUES (?, ?, ?, 1)')
            ->execute(['admin', 'admin@example.local', $hash]);

        $configContent = "<?php\nreturn " . var_export([
            'app' => [
                'name' => 'Software Artifacts Hub',
                'base_url' => $baseUrl,
                'debug' => true,
                'timezone' => 'UTC',
                'installed' => true,
            ],
            'db' => [
                'host' => $dbHost,
                'port' => $dbPort,
                'database' => $dbName,
                'username' => $dbUser,
                'password' => $dbPass,
                'charset' => 'utf8mb4',
            ],
            'security' => [
                'session_name' => 'artifacts_session',
                'csrf_key' => '_csrf',
                'max_upload_mb' => 512,
                'allowed_extensions' => ['rpm', 'deb', 'exe', 'msi', 'zip', 'gz', 'appimage', 'pkg', 'dmg'],
            ],
            'storage' => [
                'artifacts_path' => __DIR__ . '/../storage/artifacts',
                'logs_path' => __DIR__ . '/../storage/logs',
            ],
        ], true) . ";\n";

        file_put_contents($configTarget, $configContent);
        file_put_contents($lockFile, 'installed:' . date(DATE_ATOM));
        $success = true;
    } catch (Throwable $e) {
        $errors[] = 'Błąd instalacji: ' . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="pl"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Instalator Artifacts Hub</title><link rel="stylesheet" href="/assets/css/app.css">
</head><body class="guest-body"><div class="guest-wrap">
<section class="card form-card"><h1>Instalator Artifacts Hub</h1>
<?php foreach($errors as $error): ?><div class="alert danger"><?= htmlspecialchars($error) ?></div><?php endforeach; ?>
<?php if($success): ?>
<div class="alert success">Instalacja zakończona sukcesem.</div>
<p>Konto administratora utworzone:<br>login: <strong>admin</strong><br>hasło: <strong>admin</strong></p>
<p><a class="btn btn-primary" href="/login">Przejdź do logowania</a></p>
<?php else: ?>
<form method="post" class="grid two">
<label>DB Host<input name="db_host" value="127.0.0.1" required></label>
<label>DB Port<input name="db_port" value="3306" required></label>
<label>DB Name<input name="db_name" value="artifacts_hub" required></label>
<label>DB User<input name="db_user" value="root" required></label>
<label>DB Pass<input type="password" name="db_pass"></label>
<label>Base URL<input name="base_url" value="http://localhost:8000" required></label>
<div class="full"><button class="btn btn-primary">Zainstaluj</button></div>
</form>
<?php endif; ?></section></div></body></html>
