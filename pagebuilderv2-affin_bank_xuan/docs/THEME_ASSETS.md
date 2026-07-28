# Affin theme assets

Public pages load CSS/JS from the Affin demo theme under the Laravel `public` folder.

## Where files go

Copy the contents of the legacy theme’s `themes/demo/public` folder (the `css` and `js` directories) into:

```text
apps/api/public/themes/demo/
  css/   ← all .css files (style.css, affin-*-themes.css, …)
  js/    ← all .js files (custom.js, widgets.js, …)
```

Do **not** copy `.DS_Store` or nested `themes/demo/public` wrappers — only `css/` and `js/` directly under `themes/demo/`.

URLs after copy:

```text
http://localhost:8000/themes/demo/css/style.css
http://localhost:8000/themes/demo/js/custom.js
```

## How they are wired

1. `App\Support\ThemeAssets` scans `public/themes/demo/css` and `…/js`.
2. `PublicPageController` passes `themeCss` / `themeJs` into the Blade view.
3. `resources/views/public/page.blade.php` loads:
   - Bootstrap 4 + jQuery UI (CDN)
   - every theme CSS URL
   - page CSS (`$renderCss`)
   - jQuery + Popper + Bootstrap JS (CDN)
   - every theme JS URL

Skipped automatically: hidden files, `*-bak.js`, and `.min.js` when the non-min file exists.

## Homepage

`/` renders the published page with slug `home`. Theme CSS applies to that HTML. For Affin look-and-feel, Home markup must use Affin theme classes (edit/publish in the admin), not only load the assets.
