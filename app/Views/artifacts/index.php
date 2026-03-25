<section class="card"><div class="row between"><h2>Artifacty</h2><a class="btn btn-primary" href="/artifacts/upload">Upload</a></div>
<form method="get" class="row">
<select name="repository_id"><option value="0">Wybierz repo</option><?php foreach($repositories as $repo): ?><option value="<?= (int)$repo['id'] ?>" <?= $selectedRepoId===(int)$repo['id']?'selected':'' ?>><?= e($repo['name']) ?></option><?php endforeach; ?></select>
<button class="btn btn-secondary">Filtruj</button>
</form>
<div class="table-wrap"><table><thead><tr><th>Nazwa</th><th>Wersja</th><th>Status</th><th>Checksum</th><th>Pobrania</th><th></th></tr></thead><tbody>
<?php foreach($artifacts as $a): ?><tr>
<td><?= e($a['name']) ?></td><td><?= e($a['version']) ?></td><td><span class="badge"><?= e($a['status']) ?></span></td><td><code><?= e(substr($a['checksum_sha256'],0,12)) ?>...</code></td><td><?= (int)$a['download_count'] ?></td>
<td><a class="btn btn-secondary" href="/artifacts/download?id=<?= (int)$a['id'] ?>">Pobierz</a></td>
</tr><?php endforeach; ?></tbody></table></div></section>
