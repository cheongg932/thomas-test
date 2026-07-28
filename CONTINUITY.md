# Continuity log — PageBuilder V2

Last updated: 2026-07-09 (presentation sprint)

## Status: runnable local MVP — demoable tonight

Servers verified: admin `:5173`, API/public `:8000`, login OK, AI null-key hint OK.

### Done

- [x] Repo at project root (`pagebuilderv2`)
- [x] Laravel API + Sanctum auth
- [x] React admin shell (modern dark UI)
- [x] GrapesJS editor (save draft / publish / preview)
- [x] Custom HTML block + custom head/body scripts + basic SEO fields
- [x] AI generate endpoint with **null-key hint** → Settings → AI
- [x] Modules: Pages, Products, Menus, Users, Media
- [x] Settings UI for AI / storage / DB (local defaults)
- [x] Seeded demo data
- [x] `npm run dev` starts API + admin together

### Recent fix (2026-07-09)

- [x] Editor now shows **Blocks panel** (drag-and-drop), loads existing Home HTML, Styles/Layers tabs for editing — not AI-only
- [x] Database switched to **MySQL** (`pagebuilderv2` on `127.0.0.1`) — see `apps/api/.env`

### AffinCMS migration (2026-07-17)

- [x] Migration plan: `docs/AFFINCMS_MIGRATION_PLAN.md`
- [x] Separate admin sidebar screens per module type (Promotions / Announcements / News / Articles)
- [x] `php artisan affin:migrate-from-legacy` importer
- [x] Extract helper: `scripts/extract_affincms_tables.php`
- [ ] Load `affincms_legacy` DB from dump and run importer on this machine

### Affin theme assets (2026-07-23)

- [x] Copy `themes/demo/public` CSS+JS into `apps/api/public/themes/demo`
- [x] Public pages load theme CSS/JS (`docs/THEME_ASSETS.md`)
- [x] GrapesJS canvas injects theme CSS for WYSIWYG preview
- [ ] Optional: register Affin PHPageBuilder blocks as GrapesJS blocks

### Not done yet (add slowly after presentation)

- [ ] Real OpenAI/Anthropic/Gemini HTTP calls (currently: key required, then structured placeholder generation)
- [ ] Apply Settings storage credentials to runtime S3 disk dynamically
- [ ] Live DB switch from Settings (fields saved only; still need `.env` + migrate)
- [ ] GrapesJS style manager polish / hide stock chrome further
- [ ] Revisions, schedule publish, redirects, sitemap
- [ ] GitHub Actions split deploy
- [ ] Multi-tenant
- [ ] Phase 2 AffinCMS: media binaries, menus, FAQ/branches/etc.

### Demo credentials

- Email: `admin@pagebuilder.test`
- Password: `password`

### Presentation path

1. `npm run dev`
2. Login → Dashboard
3. Pages → Edit Home → drag blocks / AI (shows Settings hint without key)
4. Publish → open http://localhost:8000
5. Show Products / Menus / Settings

### Docs

- Setup/serve: [docs/SETUP_AND_SERVE.md](./docs/SETUP_AND_SERVE.md)
- Progress vs plan: [docs/PROGRESS.md](./docs/PROGRESS.md)
- Plan: [.cursor/plans/cms_pagebuilder_plan_72fd36a3.plan.md](./.cursor/plans/cms_pagebuilder_plan_72fd36a3.plan.md)

### How to resume in a new chat

Tell the agent:

> Continue PageBuilder V2 in this repo. Read `docs/SETUP_AND_SERVE.md`, `docs/PROGRESS.md`, and `CONTINUITY.md`, then keep building from “Not done yet”.
