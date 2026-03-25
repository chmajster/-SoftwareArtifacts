<section class="card"><h2>Publiczne repozytoria</h2><div class="grid cards">
<?php foreach ($repositories as $repo): ?>
<article class="card inner"><h3><?= e($repo['name']) ?></h3><p><?= e($repo['description']) ?></p><a class="btn btn-primary" href="/r?slug=<?= e($repo['slug']) ?>">Otwórz</a></article>
<?php endforeach; ?>
</div></section>
