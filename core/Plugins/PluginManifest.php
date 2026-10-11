<?php

namespace Cms\Plugins;

class PluginManifest
{
    public function __construct(
        public readonly string $slug,
        public readonly string $name,
        public readonly string $version,
        public readonly string $description,
        public readonly string $author,
        public readonly string $main,
        public readonly string $namespace,
        public readonly string $path,
    ) {
    }

    public static function fromFile(string $manifestPath): self
    {
        $data = json_decode(file_get_contents($manifestPath), true) ?: [];
        $pluginPath = dirname($manifestPath);

        return new self(
            slug: $data['slug'] ?? basename($pluginPath),
            name: $data['name'] ?? $data['slug'] ?? basename($pluginPath),
            version: $data['version'] ?? '1.0.0',
            description: $data['description'] ?? '',
            author: $data['author'] ?? '',
            main: $data['main'] ?? 'Plugin.php',
            namespace: $data['namespace'] ?? '',
            path: $pluginPath,
        );
    }

    public function mainClass(): string
    {
        return rtrim($this->namespace, '\\') . '\\' . pathinfo($this->main, PATHINFO_FILENAME);
    }
}
