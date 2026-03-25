<!doctype html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Artifacts Hub</title>
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="guest-body">
<div class="guest-wrap">
  <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($msg = flash('error')): ?><div class="alert danger"><?= e($msg) ?></div><?php endif; ?>
  <?= $content ?>
</div>
<script src="/assets/js/app.js"></script>
</body>
</html>
