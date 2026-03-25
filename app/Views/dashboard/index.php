<section class="grid stats">
  <div class="card stat"><div>Repozytoria</div><strong><?= (int)$stats['repositories'] ?></strong></div>
  <div class="card stat"><div>Artifacty</div><strong><?= (int)$stats['artifacts'] ?></strong></div>
  <div class="card stat"><div>Pobrania</div><strong><?= (int)$stats['downloads'] ?></strong></div>
</section>
<section class="card">
<h3>Ostatnie aktywności</h3>
<div class="table-wrap"><table><thead><tr><th>Akcja</th><th>Encja</th><th>Data</th></tr></thead><tbody>
<?php foreach ($activities as $a): ?>
<tr><td><span class="badge"><?= e($a['action']) ?></span></td><td><?= e($a['entity_type']) ?></td><td><?= e($a['created_at']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
</section>
