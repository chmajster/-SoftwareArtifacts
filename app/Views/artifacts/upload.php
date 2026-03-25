<section class="card form-card"><h2>Upload artifactu</h2>
<form method="post" action="/artifacts/upload" enctype="multipart/form-data" class="grid two">
<input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
<label>Repozytorium<select name="repository_id" required><?php foreach($repositories as $repo): ?><option value="<?= (int)$repo['id'] ?>"><?= e($repo['name']) ?></option><?php endforeach; ?></select></label>
<label>Nazwa<input name="name" required></label>
<label>Wersja<input name="version" required></label>
<label>Status<select name="status"><option>draft</option><option>published</option><option>archived</option></select></label>
<label>OS<select name="os_id"><?php foreach($oss as $i): ?><option value="<?= (int)$i['id'] ?>"><?= e($i['name']) ?></option><?php endforeach; ?></select></label>
<label>Architektura<select name="architecture_id"><?php foreach($archs as $i): ?><option value="<?= (int)$i['id'] ?>"><?= e($i['name']) ?></option><?php endforeach; ?></select></label>
<label>Typ pakietu<select name="package_type_id"><?php foreach($packageTypes as $i): ?><option value="<?= (int)$i['id'] ?>"><?= e($i['name']) ?></option><?php endforeach; ?></select></label>
<label>Plik<input type="file" name="artifact" required></label>
<label class="full">Opis<textarea name="description"></textarea></label>
<label class="full">Changelog<textarea name="changelog"></textarea></label>
<label><input type="checkbox" name="is_latest"> Oznacz jako latest</label>
<label><input type="checkbox" name="is_hidden"> Ukryj artifact</label>
<label><input type="checkbox" name="is_deprecated"> Deprecated</label>
<div class="full"><button class="btn btn-primary">Wyślij</button></div>
</form></section>
