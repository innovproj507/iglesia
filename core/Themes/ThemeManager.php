<?php

namespace Cms\Themes;

class ThemeManager
{
    /** @var array<string, array> */
    private array $themes = [];
    private bool $discovered = false;

    public function __construct(private string $themesPath, private string $activeSlug)
    {
    }

    public function discover(): array
    {
        if ($this->discovered) {
            return $this->themes;
        }

        foreach (glob($this->themesPath . '/*/theme.json') ?: [] as $themeFile) {
            $data = json_decode(file_get_contents($themeFile), true) ?: [];
            $slug = $data['slug'] ?? basename(dirname($themeFile));
            $this->themes[$slug] = array_merge($data, ['slug' => $slug, 'path' => dirname($themeFile)]);
        }

        $this->discovered = true;

        return $this->themes;
    }

    public function active(): array
    {
        $this->discover();

        return $this->themes[$this->activeSlug] ?? reset($this->themes) ?: [];
    }

    public function activeSlug(): string
    {
        return $this->activeSlug;
    }

    public function templatesPath(): string
    {
        $theme = $this->active();

        return ($theme['path'] ?? $this->themesPath . '/' . $this->activeSlug) . '/templates';
    }

    /**
     * WordPress-style template hierarchy resolution.
     * Returns the first logical template name (without extension) that exists
     * as a physical file in the active theme, or null if none matches.
     */
    public function resolveHome(): ?string
    {
        return $this->firstExisting(['front-page', 'home', 'index']);
    }

    public function resolveSingle(string $postType, string $slug): ?string
    {
        return $this->firstExisting([
            "single-{$postType}-{$slug}",
            "single-{$postType}",
            'single',
            'index',
        ]);
    }

    public function resolvePage(string $slug): ?string
    {
        return $this->firstExisting(["page-{$slug}", 'page', 'index']);
    }

    public function resolveArchive(string $postType): ?string
    {
        return $this->firstExisting(["archive-{$postType}", 'archive', 'index']);
    }

    public function resolve404(): ?string
    {
        return $this->firstExisting(['404']);
    }

    private function firstExisting(array $candidates): ?string
    {
        foreach ($candidates as $name) {
            if (is_file($this->templatesPath() . '/' . $name . '.php')) {
                return $name;
            }
        }

        return null;
    }
}
