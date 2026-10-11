<?php

namespace Cms;

use Cms\Database\Connection;
use Cms\Hooks\HookManager;
use Cms\Settings\OptionsRepository;

class Application
{
    private static ?Application $instance = null;

    private Container $container;
    private string $basePath;
    private array $config = [];

    private function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->container = new Container();
        $this->container->instance(Container::class, $this->container);
        $this->container->instance(Application::class, $this);
        $this->container->singleton(HookManager::class, fn () => new HookManager());
    }

    public static function create(string $basePath): self
    {
        return self::$instance = new self($basePath);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            throw new \RuntimeException('Application has not been bootstrapped yet.');
        }

        return self::$instance;
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function basePath(string $path = ''): string
    {
        return $this->basePath . ($path !== '' ? '/' . ltrim($path, '/\\') : '');
    }

    public function hooks(): HookManager
    {
        return $this->container->make(HookManager::class);
    }

    public function loadConfig(array $config): void
    {
        $this->config = $config;
    }

    public function config(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function isInstalled(): bool
    {
        return file_exists($this->basePath('.env'));
    }

    public function db(): Connection
    {
        return $this->container->make(Connection::class);
    }

    public function options(): OptionsRepository
    {
        return $this->container->make(OptionsRepository::class);
    }
}
