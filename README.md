# Software Artifacts Hub (PHP + MySQL)

Kompletna aplikacja webowa do zarządzania repozytoriami i publikowania software artifacts.

## Stack

- PHP 8+
- MySQL / MariaDB
- Czysty HTML/CSS/JavaScript (bez frameworków)

## Struktura projektu

```text
/public
/app
  /Controllers
  /Core
  /Helpers
  /Models
  /Services
  /Views
/config
/install
  /sql
/storage
  /artifacts
  /logs
  /cache
```

## Instalacja

1. Skonfiguruj vhost/document root na katalog `public/`.
2. Wejdź w przeglądarce na `/install/`.
3. Uzupełnij formularz DB i uruchom instalację.
4. Instalator:
   - sprawdza podstawowe wymagania,
   - importuje `install/sql/schema.sql` i `install/sql/seed.sql`,
   - tworzy konto administratora `admin/admin` (hasło hashowane przez `password_hash`),
   - zapisuje `config/config.local.php`,
   - tworzy `storage/installed.lock` i blokuje ponowne uruchomienie instalatora.

## Logowanie

Po instalacji:
- login: `admin`
- hasło: `admin`

## API (podstawowe)

- `GET /public/metadata.php?slug={repo_slug}` – JSON metadata repozytorium.
- `GET /artifacts/download?id={artifact_id}` – pobieranie pliku artifactu.

## Bezpieczeństwo

- `password_hash` i `password_verify`
- CSRF tokeny
- Prepared statements (PDO)
- Escaping danych wyjściowych (`htmlspecialchars`)
- Walidacja uploadów (rozszerzenia, rozmiar)
- Bezpieczne sesje (httponly, samesite)

## Rozszerzalność

System jest przygotowany pod dalszą rozbudowę m.in. o:
- webhooki,
- upload CI/CD przez tokeny,
- podpisy GPG,
- prywatne feedy i integracje.
