<?php

namespace Cms\Content;

class PostTypeRegistry
{
    /** @var array<string, PostType> */
    private array $types = [];
    private bool $booted = false;

    public function register(string $slug, array $options = []): PostType
    {
        $type = new PostType(
            slug: $slug,
            label: $options['label'] ?? ucfirst($slug) . 's',
            labelSingular: $options['label_singular'] ?? ucfirst($slug),
            hasArchive: $options['has_archive'] ?? false,
            supports: $options['supports'] ?? ['title', 'editor'],
            menuIcon: $options['menu_icon'] ?? 'file-text',
            showInAdminMenu: $options['show_in_admin_menu'] ?? true,
        );

        $this->types[$slug] = $type;

        return $type;
    }

    public function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->register('post', [
            'label' => 'Entradas',
            'label_singular' => 'Entrada',
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'menu_icon' => 'newspaper',
        ]);

        $this->register('page', [
            'label' => 'Páginas',
            'label_singular' => 'Página',
            'has_archive' => false,
            'supports' => ['title', 'editor'],
            'menu_icon' => 'file',
        ]);

        do_action('cms.post_types.register', $this);

        $this->booted = true;
    }

    public function get(string $slug): ?PostType
    {
        return $this->types[$slug] ?? null;
    }

    /** @return PostType[] */
    public function all(): array
    {
        return $this->types;
    }
}
