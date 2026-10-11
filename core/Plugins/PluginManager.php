<?php

namespace Cms\Plugins;

use Cms\Database\Connection;
use Cms\Database\Migrator;
use Cms\Hooks\HookManager;
use Composer\Autoload\ClassLoader;

class PluginManager
{
    /** @var array<string, PluginManifest> */
    private array $manifests = [];
    private bool $discovered = false;

    /** @var PluginInterface[] */
    private array $bootedInstances = [];

    public function __construct(
        private Connection $connection,
        private HookManager $hooks,
        private ClassLoader $loader,
        private string $pluginsPath,
    ) {
    }

    /** @return PluginManifest[] */
    public function discover(): array
    {
        if ($this->discovered) {
            return $this->manifests;
        }

        foreach (glob($this->pluginsPath . '/*/plugin.json') ?: [] as $manifestFile) {
            $manifest = PluginManifest::fromFile($manifestFile);
            $this->manifests[$manifest->slug] = $manifest;
        }

        $this->discovered = true;

        return $this->manifests;
    }

    public function manifest(string $slug): ?PluginManifest
    {
        $this->discover();

        return $this->manifests[$slug] ?? null;
    }

    public function getInstalled(): array
    {
        return $this->connection->medoo()->select('plugins', '*');
    }

    public function getActiveSlugs(): array
    {
        return $this->connection->medoo()->select('plugins', 'slug', ['status' => 'active']);
    }

    public function isActive(string $slug): bool
    {
        return in_array($slug, $this->getActiveSlugs(), true);
    }

    public function install(string $slug): void
    {
        $manifest = $this->manifest($slug);

        if (!$manifest) {
            throw new \RuntimeException("Plugin [{$slug}] not found.");
        }

        $existing = $this->connection->medoo()->get('plugins', 'id', ['slug' => $slug]);

        if (!$existing) {
            $this->connection->insert('plugins', [
                'slug' => $slug,
                'name' => $manifest->name,
                'version_installed' => $manifest->version,
                'status' => 'inactive',
                'installed_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $migrationsPath = $manifest->path . '/database/migrations';

        if (is_dir($migrationsPath)) {
            (new Migrator($this->connection))->run($migrationsPath, $slug);
        }
    }

    public function activate(string $slug): void
    {
        $this->install($slug);
        $manifest = $this->manifest($slug);
        $instance = $this->loadInstance($manifest);
        $instance->activate();

        $this->connection->update('plugins', [
            'status' => 'active',
            'activated_at' => date('Y-m-d H:i:s'),
        ], ['slug' => $slug]);
    }

    public function deactivate(string $slug): void
    {
        $manifest = $this->manifest($slug);

        if ($manifest) {
            $this->loadInstance($manifest)->deactivate();
        }

        $this->connection->update('plugins', ['status' => 'inactive'], ['slug' => $slug]);
    }

    public function uninstall(string $slug): void
    {
        $manifest = $this->manifest($slug);

        if ($manifest) {
            $this->loadInstance($manifest)->uninstall();
        }

        $this->connection->delete('plugins', ['slug' => $slug]);
        $this->connection->delete('migrations', ['group' => $slug]);
    }

    public function bootActive(): void
    {
        foreach ($this->getActiveSlugs() as $slug) {
            $manifest = $this->manifest($slug);

            if (!$manifest) {
                continue;
            }

            $instance = $this->loadInstance($manifest);
            $instance->register($this->hooks);
        }
    }

    private function loadInstance(PluginManifest $manifest): PluginInterface
    {
        if (isset($this->bootedInstances[$manifest->slug])) {
            return $this->bootedInstances[$manifest->slug];
        }

        $srcPath = $manifest->path . '/src/';

        if ($manifest->namespace && is_dir($srcPath)) {
            $this->loader->addPsr4($manifest->namespace, $srcPath);
        }

        $mainFile = $manifest->path . '/' . $manifest->main;
        $mainClass = $manifest->mainClass();

        if (!class_exists($mainClass) && is_file($mainFile)) {
            require_once $mainFile;
        }

        return $this->bootedInstances[$manifest->slug] = new $mainClass();
    }
}
