<section class="card form-card"><h2>Logowanie</h2>
<form method="post" action="/login">
  <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
  <label>Email<input type="email" name="email" required></label>
  <label>Hasło<input type="password" name="password" required></label>
  <button class="btn btn-primary">Zaloguj</button>
</form>
<p>Brak konta? <a href="/register">Rejestracja</a></p>
<p><a href="/reset-password">Reset hasła</a></p></section>
