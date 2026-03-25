<?php $user = $_SESSION['user'] ?? null; ?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($this->config['app']['name'] ?? 'Artifacts Hub') ?></title>
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div class="app-shell">
  <aside class="sidebar">
    <h1>Artifacts Hub</h1>
    <a href="/dashboard">Dashboard</a>
    <a href="/repositories">Repozytoria</a>
    <a href="/artifacts">Artifacty</a>
    <?php if (($user['role'] ?? '') === 'admin'): ?><a href="/admin">Admin</a><?php endif; ?>
    <a href="/">Publiczne</a>
  </aside>
  <main class="main">
    <header class="topbar">
      <div><?= e($user['username'] ?? 'Gość') ?></div>
      <?php if ($user): ?>
      <form method="post" action="/logout"><button class="btn btn-secondary">Wyloguj</button></form>
      <?php endif; ?>
    </header>
    <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert danger"><?= e($msg) ?></div><?php endif; ?>
    <?= $content ?>
  </main>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
