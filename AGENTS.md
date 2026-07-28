# AGENTS.md

## Cursor Cloud specific instructions

### What this project is
PageBuilder V2 — an AI-capable CMS with a **Laravel API + public site** (`apps/api`) and a **React admin** with a GrapesJS page builder (`apps/admin`). One command runs both (see `package.json`).

### Non-obvious setup facts
- The application source ships **inside `pagebuilderv2-affin_bank_xuan.zip`**, not as tracked files. The repo only tracks the zip plus a few root files (`package.json`, `README.md`, etc.). The `apps/`, `docs/`, and `scripts/` directories are extracted from the zip into the repo root and are **not committed** (leave them untracked; do not `git add apps/`). The startup update script re-extracts the zip if `apps/` is missing.
- **PHP 8.4 is required**, not 8.3 as the README suggests. `composer.lock` resolves to Symfony 8.1 packages that need `php >= 8.4.1`. The default `php` is aliased to `php8.4` via update-alternatives.
- Local dev uses **SQLite**, not MySQL (README documents MySQL). `apps/api/.env` sets `DB_CONNECTION=sqlite` and `DB_DATABASE=/workspace/apps/api/database/database.sqlite`. Session/cache/queue use the `database` driver, which works fine on SQLite.

### Running (dev)
- From repo root: `npm run dev` (see `package.json`) starts both services:
  - API + public site on `http://127.0.0.1:8000` via `scripts/dev-api.sh` (a custom PHP built-in server + `server-router.php`, not `artisan serve`).
  - Admin (Vite) on `http://127.0.0.1:5173`. Vite proxies `/api`, `/storage`, `/preview`, `/p/` to the API.
- Demo logins (seeded): `admin@pagebuilder.test` / `password` (admin), `editor@pagebuilder.test` / `password` (editor).

### Reconstructing DB/env (only if the VM snapshot lost `apps/api/.env` or the SQLite file)
Run inside `apps/api`: `cp .env.example .env` then set the SQLite values above, `php artisan key:generate`, `touch database/database.sqlite`, `php artisan migrate --force`, `php artisan db:seed --force`, `php artisan storage:link`. The update script intentionally does **not** run migrations/seed.

### Lint / test / build
- Admin: `npm run lint` (oxlint; warnings only) and `npm run build` (`tsc -b && vite build`) — run in `apps/admin`.
- API: `php artisan test` (PHPUnit) and `./vendor/bin/pint --test` (style check) — run in `apps/api`.
- Known pre-existing failure: `Tests\Feature\ExampleTest::test_the_application_returns_a_successful_response` fails because the stock test does `GET /` (which queries the `pages` table) against the `:memory:` test DB in `phpunit.xml` without running migrations. This is unrelated to environment setup.

### AI features
The AI panel (Settings → AI) requires an API key; without one it returns a clear "key required" hint. No key is needed for the rest of the app.
