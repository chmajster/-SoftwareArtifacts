<section class="grid stats">
  <div class="card stat"><div>Użytkownicy</div><strong><?= count($users) ?></strong></div>
  <div class="card stat"><div>Repozytoria</div><strong><?= count($repositories) ?></strong></div>
  <div class="card stat"><div>Artifacty</div><strong><?= count($artifacts) ?></strong></div>
</section>
<section class="card"><h3>Słowniki globalne</h3>
<form method="post" action="/admin/taxonomies" class="grid three">
<input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
<select name="table"><option value="operating_systems">OS</option><option value="architectures">Architektury</option><option value="package_types">Typy paczek</option><option value="release_channels">Kanały</option></select>
<input name="name" placeholder="Nazwa" required>
<input name="slug" placeholder="slug" required>
<button class="btn btn-primary">Dodaj</button>
</form>
</section>
<section class="card"><h3>Użytkownicy</h3><div class="table-wrap"><table><thead><tr><th>ID</th><th>Login</th><th>Email</th><th>Rola</th><th>Status</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?= (int)$u['id'] ?></td><td><?= e($u['username']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role_name']) ?></td><td><?= (int)$u['is_blocked'] ? 'blocked' : 'active' ?></td></tr><?php endforeach; ?></tbody></table></div></section>
