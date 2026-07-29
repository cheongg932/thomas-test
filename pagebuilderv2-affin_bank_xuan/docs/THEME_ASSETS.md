# Affin theme assets

Public pages load CSS/JS from the Affin demo theme under the Laravel `public` folder.

## Correct filepath (important)

From the theme zip, the assets live at:

```text
themes/demo/public/css/
themes/demo/public/js/
```

Copy those **into the Laravel public folder** like this:

```text
apps/api/public/themes/demo/css/   ← contents of zip's themes/demo/public/css
apps/api/public/themes/demo/js/    ← contents of zip's themes/demo/public/js
```

Full example on Windows:

```text
pagebuilderv2-xuan-main\apps\api\public\themes\demo\css\style.css
pagebuilderv2-xuan-main\apps\api\public\themes\demo\js\custom.js
```

Do **not** leave an extra nested wrapper if you can avoid it:

```text
❌ apps/api/public/themes/demo/themes/demo/public/css
❌ apps/api/public/themes/demo/public/css   (works, but not preferred)
✅ apps/api/public/themes/demo/css
```

`ThemeAssets::resolveThemeRoot()` accepts the preferred path and the common nested mistakes so links still work.

## URLs after copy

```text
http://localhost:8000/themes/demo/css/style.css
http://localhost:8000/themes/demo/js/custom.js
```

## How they are wired

1. `App\Support\ThemeAssets::manifest('demo')` finds `css/` + `js/` and builds public URLs.
2. `PublicPageController` passes `themeCss` / `themeJs` into the Blade view.
3. `resources/views/public/page.blade.php` loads Bootstrap/jQuery (CDN) then every theme CSS/JS file.

Skipped automatically: hidden files, `*-bak.js`, and `.min.js` when the non-min file exists.

## Quick check

In Ubuntu (from your project root):

```bash
ls apps/api/public/themes/demo/css | head
ls apps/api/public/themes/demo/js | head
```

Then open `http://localhost:8000/themes/demo/css/style.css` — you should see CSS, not 404.

## Homepage look

Theme CSS applies to page HTML. For an Affin lookalike, published page markup must use Affin theme classes (migrated Affin pages or Affin block HTML), not only load the assets.
