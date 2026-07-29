<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

class ThemeAssets
{
    /**
     * CSS + JS public URLs for the active theme.
     *
     * @return array{css: list<string>, js: list<string>, root: string|null}
     */
    public static function manifest(string $theme = 'demo'): array
    {
        $root = self::resolveThemeRoot($theme);

        if ($root === null) {
            return [
                'css' => [],
                'js' => [],
                'root' => null,
            ];
        }

        return [
            'css' => self::urlsIn($root['absolute'].DIRECTORY_SEPARATOR.'css', $root['url'].'/css', 'css'),
            'js' => self::urlsIn($root['absolute'].DIRECTORY_SEPARATOR.'js', $root['url'].'/js', 'js'),
            'root' => $root['absolute'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function cssUrls(string $theme = 'demo'): array
    {
        return self::manifest($theme)['css'];
    }

    /**
     * @return list<string>
     */
    public static function jsUrls(string $theme = 'demo'): array
    {
        return self::manifest($theme)['js'];
    }

    /**
     * Resolve where the theme's css/js folders live under public/.
     *
     * Supported layouts (first match wins):
     * 1) public/themes/demo/css + js          ← preferred
     * 2) public/themes/demo/public/css + js   ← copied whole zip "public" folder
     * 3) public/themes/demo/themes/demo/public/css + js  ← accidental nested copy
     *
     * @return array{absolute: string, url: string}|null
     */
    public static function resolveThemeRoot(string $theme = 'demo'): ?array
    {
        $candidates = [
            [
                'absolute' => public_path("themes/{$theme}"),
                'url' => "themes/{$theme}",
            ],
            [
                'absolute' => public_path("themes/{$theme}/public"),
                'url' => "themes/{$theme}/public",
            ],
            [
                'absolute' => public_path("themes/{$theme}/themes/{$theme}/public"),
                'url' => "themes/{$theme}/themes/{$theme}/public",
            ],
        ];

        foreach ($candidates as $candidate) {
            if (
                is_dir($candidate['absolute'].DIRECTORY_SEPARATOR.'css')
                || is_dir($candidate['absolute'].DIRECTORY_SEPARATOR.'js')
            ) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private static function urlsIn(string $absoluteDir, string $urlPrefix, string $extension): array
    {
        if (! is_dir($absoluteDir)) {
            return [];
        }

        $files = collect(File::files($absoluteDir))
            ->filter(fn ($file) => strtolower($file->getExtension()) === $extension)
            ->reject(fn ($file) => self::shouldSkip($file->getFilename()))
            ->map(fn ($file) => $file->getFilename())
            ->values();

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
            ->map(fn (string $name) => asset(trim($urlPrefix, '/').'/'.$name))
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
