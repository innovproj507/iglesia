<?php

use Cms\Application;
use Cms\Database\Connection;
use Cms\Hooks\HookManager;
use Cms\Settings\OptionsRepository;
use Cms\Themes\View;

if (!function_exists('app')) {
    function app(?string $abstract = null): mixed
    {
        $application = Application::getInstance();

        if ($abstract === null) {
            return $application;
        }

        return $application->container()->make($abstract);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return app()->basePath($path);
    }
}

if (!function_exists('plugin_path')) {
    function plugin_path(string $path = ''): string
    {
        return base_path('plugins/' . ltrim($path, '/\\'));
    }
}

if (!function_exists('theme_path')) {
    function theme_path(string $path = ''): string
    {
        return base_path('themes/' . ltrim($path, '/\\'));
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage/' . ltrim($path, '/\\'));
    }
}

if (!function_exists('add_action')) {
    function add_action(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        app(HookManager::class)->addAction($hook, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('do_action')) {
    function do_action(string $hook, mixed ...$args): void
    {
        app(HookManager::class)->doAction($hook, ...$args);
    }
}

if (!function_exists('remove_action')) {
    function remove_action(string $hook, callable $callback, int $priority = 10): bool
    {
        return app(HookManager::class)->removeAction($hook, $callback, $priority);
    }
}

if (!function_exists('has_action')) {
    function has_action(string $hook): bool
    {
        return app(HookManager::class)->hasAction($hook);
    }
}

if (!function_exists('add_filter')) {
    function add_filter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void
    {
        app(HookManager::class)->addFilter($hook, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('apply_filters')) {
    function apply_filters(string $hook, mixed $value, mixed ...$args): mixed
    {
        return app(HookManager::class)->applyFilters($hook, $value, ...$args);
    }
}

if (!function_exists('remove_filter')) {
    function remove_filter(string $hook, callable $callback, int $priority = 10): bool
    {
        return app(HookManager::class)->removeFilter($hook, $callback, $priority);
    }
}

if (!function_exists('db')) {
    function db(): Connection
    {
        return app(Connection::class);
    }
}

if (!function_exists('option')) {
    function option(string $name, mixed $default = null): mixed
    {
        return app(OptionsRepository::class)->get($name, $default);
    }
}

if (!function_exists('option_array')) {
    function option_array(string $name, array $default = []): array
    {
        $value = option($name, $default);

        return is_array($value) ? $value : $default;
    }
}

if (!function_exists('update_option')) {
    function update_option(string $name, mixed $value, bool $autoload = true): void
    {
        app(OptionsRepository::class)->set($name, $value, $autoload);
    }
}

if (!function_exists('view')) {
    function view(string $name, array $data = []): string
    {
        return app(View::class)->render($name, $data);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return \Cms\Support\Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $to): never
    {
        header('Location: ' . $to);
        exit;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/\\');
    }
}
