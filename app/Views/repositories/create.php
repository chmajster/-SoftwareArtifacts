<section class="card form-card"><h2>Utwórz repozytorium</h2>
<form method="post" action="/repositories/create" class="grid two">
<input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
<label>Nazwa<input name="name" required></label>
<label>Slug<input name="slug" required pattern="[a-z0-9-]+"></label>
<label class="full">Opis<textarea name="description"></textarea></label>
<label>Widoczność<select name="visibility"><option value="private">Prywatne</option><option value="public">Publiczne</option></select></label>
<label>Wersja<input name="version" value="1.0.0"></label>
<label>OS<select name="os_id"><?php foreach($oss as $i): ?><option value="<?= (int)$i['id'] ?>"><?= e($i['name']) ?></option><?php endforeach; ?></select></label>
<label>Architektura<select name="architecture_id"><?php foreach($archs as $i): ?><option value="<?= (int)$i['id'] ?>"><?= e($i['name']) ?></option><?php endforeach; ?></select></label>
<label>Typ paczki<select name="package_type_id"><?php foreach($packageTypes as $i): ?><option value="<?= (int)$i['id'] ?>"><?= e($i['name']) ?></option><?php endforeach; ?></select></label>
<label>Kanał<select name="release_channel_id"><?php foreach($channels as $i): ?><option value="<?= (int)$i['id'] ?>"><?= e($i['name']) ?></option><?php endforeach; ?></select></label>
<label><input type="checkbox" name="listing_enabled" checked> Włącz listing plików</label>
<label><input type="checkbox" name="is_custom"> Repozytorium custom</label>
<div class="full"><button class="btn btn-primary">Zapisz</button></div>
</form></section>
