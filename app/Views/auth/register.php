<section class="card form-card"><h2>Rejestracja</h2>
<form method="post" action="/register">
  <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
  <label>Login<input name="username" required></label>
  <label>Email<input type="email" name="email" required></label>
  <label>Hasło<input type="password" name="password" minlength="8" required></label>
  <button class="btn btn-primary">Utwórz konto</button>
</form>
</section>
