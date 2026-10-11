<?php

namespace Cms\Plugins;

use Cms\Hooks\HookManager;

interface PluginInterface
{
    /**
     * Called on every request while the plugin is active.
     * Register hooks, routes, post types, admin menus, etc. here.
     */
    public function register(HookManager $hooks): void;

    /**
     * Called once when the plugin is activated. Run migrations/seed here.
     */
    public function activate(): void;

    /**
     * Called when the plugin is deactivated. Data must NOT be deleted here.
     */
    public function deactivate(): void;

    /**
     * Called when the plugin is uninstalled. Safe to delete the plugin's own data.
     */
    public function uninstall(): void;
}
