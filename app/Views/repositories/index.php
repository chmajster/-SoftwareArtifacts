<section class="card"><div class="row between"><h2>Moje repozytoria</h2><a class="btn btn-primary" href="/repositories/create">Nowe repozytorium</a></div>
<div class="table-wrap"><table><thead><tr><th>Nazwa</th><th>Slug</th><th>Widoczność</th><th>OS</th><th>Arch</th><th>Pakiet</th></tr></thead><tbody>
<?php foreach ($repositories as $repo): ?>
<tr>
<td><?= e($repo['name']) ?></td><td><?= e($repo['slug']) ?></td>
<td><span class="badge <?= $repo['visibility'] === 'public' ? 'success' : 'muted' ?>"><?= e($repo['visibility']) ?></span></td>
<td><?= e($repo['os_name']) ?></td><td><?= e($repo['arch_name']) ?></td><td><?= e($repo['package_name']) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
</section>
