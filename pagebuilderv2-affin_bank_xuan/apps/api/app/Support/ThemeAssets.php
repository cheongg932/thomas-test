<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class ThemeAssets
{
    /**
     * Public URLs for CSS under public/themes/{theme}/css.
     *
     * @return list<string>
     */
    public static function cssUrls(string $theme = 'demo'): array
    {
        return self::assetUrls($theme, 'css', 'css');
    }

    /**
     * Public URLs for JS under public/themes/{theme}/js.
     *
     * @return list<string>
     */
    public static function jsUrls(string $theme = 'demo'): array
    {
        return self::assetUrls($theme, 'js', 'js');
    }

    /**
     * @return list<string>
     */
    private static function assetUrls(string $theme, string $subdir, string $extension): array
    {
        $dir = public_path("themes/{$theme}/{$subdir}");

        if (! is_dir($dir)) {
            return [];
        }

        $files = collect(File::files($dir))
            ->filter(fn ($file) => strtolower($file->getExtension()) === $extension)
            ->reject(fn ($file) => self::shouldSkip($file->getFilename()))
            ->map(fn ($file) => $file->getFilename())
            ->values();

        // Prefer non-.min when both foo.js and foo.min.js exist.
        if ($extension === 'js') {
            $names = $files->all();
            $files = $files->reject(function (string $name) use ($names) {
                if (! preg_match('/\.min\.js$/i', $name)) {
                    return false;
                }

                $nonMin = preg_replace('/\.min\.js$/i', '.js', $name);

                return in_array($nonMin, $names, true);
            })->values();
        }

        // Load style.css first when present so base theme wins before components.
        $sorted = $files->sort(function (string $a, string $b) {
            if ($a === 'style.css') {
                return -1;
            }
            if ($b === 'style.css') {
                return 1;
            }
            if ($a === 'custom.js') {
                return -1;
            }
            if ($b === 'custom.js') {
                return 1;
            }

            return strcmp($a, $b);
        })->values();

        return $sorted
            ->map(fn (string $name) => asset("themes/{$theme}/{$subdir}/{$name}"))
            ->all();
    }

    private static function shouldSkip(string $filename): bool
    {
        $lower = strtolower($filename);

        return str_starts_with($lower, '.')
            || str_contains($lower, '-bak.')
            || str_contains($lower, '.bak.');
    }
}
