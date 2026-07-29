<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Page;
use App\Support\ThemeAssets;

class PublicPageController extends Controller
{
    public function home()
    {
        $page = Page::query()
            ->whereNotNull('published_html')
            ->where('slug', 'home')
            ->first()
            ?? Page::query()
                ->whereNotNull('published_html')
                ->latest('published_at')
                ->first()
            // Legacy fallback
            ?? Page::query()->where('status', 'published')->where('slug', 'home')->first()
            ?? Page::query()->where('status', 'published')->latest('published_at')->first();

        if (! $page || ! $page->liveHtml()) {
            return view('public.empty');
        }

        return $this->render($page, false);
    }

    public function show(string $slug)
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where(function ($q) {
                $q->whereNotNull('published_html')
                    ->orWhere('status', 'published');
            })
            ->firstOrFail();

        if (! $page->liveHtml()) {
            abort(404);
        }

        return $this->render($page, false);
    }

    /**
     * Studio preview — always shows the current draft (compiled_*),
     * even when unpublished changes exist.
     */
    public function preview(string $slug)
    {
        $page = Page::query()->where('slug', $slug)->firstOrFail();

        return $this->render($page, true);
    }

    private function render(Page $page, bool $isPreview)
    {
        $header = Menu::query()->where('location', 'header')->where('is_active', true)->orderBy('sort_order')->get();
        $footer = Menu::query()->where('location', 'footer')->where('is_active', true)->orderBy('sort_order')->get();

        // Preview = draft working copy; Live = last published snapshot
        $html = $isPreview ? ($page->compiled_html ?: '') : ($page->liveHtml() ?: '');
        $css = $isPreview ? ($page->compiled_css ?: '') : ($page->liveCss() ?: '');
        $theme = ThemeAssets::manifest('demo');

        return view('public.page', [
            'page' => $page,
            'header' => $header,
            'footer' => $footer,
            'isPreview' => $isPreview,
            'renderHtml' => $html,
            'renderCss' => $css,
            'themeCss' => $theme['css'],
            'themeJs' => $theme['js'],
        ]);
    }
}
