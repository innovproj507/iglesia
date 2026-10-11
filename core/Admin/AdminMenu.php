<?php

namespace Cms\Admin;

use Cms\Auth\User;

class AdminMenu
{
    /** @var array<int, array> */
    private array $items = [];
    private bool $built = false;

    public function register(string $slug, string $label, string $icon, ?string $capability, string $url, ?string $parentSlug = null, int $position = 10): void
    {
        $this->items[] = compact('slug', 'label', 'icon', 'capability', 'url', 'parentSlug', 'position');
    }

    private function build(): void
    {
        if ($this->built) {
            return;
        }

        $this->register('dashboard', 'Dashboard', 'home', null, '/admin', null, 0);
        $this->register('posts', 'Entradas', 'newspaper', 'edit_posts', '/admin/posts?type=post', null, 10);
        $this->register('pages', 'Páginas', 'file', 'edit_posts', '/admin/posts?type=page', null, 20);
        $this->register('media', 'Media', 'image', 'manage_media', '/admin/media', null, 30);
        $this->register('menus', 'Menús', 'menu', 'manage_menus', '/admin/menus', null, 40);
        $this->register('users', 'Usuarios', 'users', 'manage_users', '/admin/users', null, 50);
        $this->register('plugins', 'Plugins', 'puzzle', 'manage_plugins', '/admin/plugins', null, 60);
        $this->register('themes', 'Temas', 'palette', 'manage_themes', '/admin/themes', null, 70);
        $this->register('settings', 'Ajustes', 'settings', 'manage_options', '/admin/settings', null, 80);

        do_action('admin.menu', $this);

        $this->built = true;
    }

    /** @return array<int, array> */
    public function items(?User $user): array
    {
        $this->build();

        $visible = array_filter($this->items, function (array $item) use ($user) {
            return $item['capability'] === null || ($user && $user->can($item['capability']));
        });

        usort($visible, fn ($a, $b) => $a['position'] <=> $b['position']);

        return array_values($visible);
    }
}
