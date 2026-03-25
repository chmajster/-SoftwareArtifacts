<section class="card"><h2><?= e($repository['name']) ?></h2>
<p><?= e($repository['description']) ?></p>
<div class="row"><a class="btn btn-secondary" href="<?= e($metadataUrl) ?>">Metadata JSON</a></div>
<div class="table-wrap"><table><thead><tr><th>Nazwa</th><th>Wersja</th><th>OS</th><th>Arch</th><th>Typ</th><th></th></tr></thead><tbody>
<?php foreach ($artifacts as $a): ?>
<tr><td><?= e($a['name']) ?></td><td><?= e($a['version']) ?></td><td><?= e($a['os_name']) ?></td><td><?= e($a['arch_name']) ?></td><td><?= e($a['package_name']) ?></td><td><a class="btn btn-primary" href="/artifacts/download?id=<?= (int)$a['id'] ?>">Download</a></td></tr>
<?php endforeach; ?></tbody></table></div></section>
