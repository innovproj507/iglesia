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

if (!function_exists('media_url')) {
    function media_url(?int $mediaId): ?string
    {
        if (!$mediaId) {
            return null;
        }

        $path = db()->get('media', 'file_path', ['id' => $mediaId]);

        return $path ? '/' . ltrim($path, '/') : null;
    }
}

if (!function_exists('youtube_id')) {
    function youtube_id(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $pattern = '~(?:youtube\.com/(?:watch\?v=|embed/|live/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~';

        return preg_match($pattern, $url, $m) ? $m[1] : null;
    }
}

if (!function_exists('post_image')) {
    /**
     * Imagen para tarjetas: imagen destacada, o miniatura de YouTube si el post tiene video.
     */
    function post_image(array $post): ?string
    {
        if ($url = media_url(isset($post['featured_media_id']) ? (int) $post['featured_media_id'] : null)) {
            return $url;
        }

        $id = youtube_id($post['video_url'] ?? null);

        return $id ? "https://i.ytimg.com/vi/{$id}/hqdefault.jpg" : null;
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $date): string
    {
        if (!$date || !($ts = strtotime($date))) {
            return '';
        }

        $months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        return date('j', $ts) . ' de ' . $months[(int) date('n', $ts) - 1] . ', ' . date('Y', $ts);
    }
}

if (!function_exists('service_schedule')) {
    /**
     * Horarios de servicio estructurados. Cada línea admite el formato "Título | Horario | Día".
     */
    function service_schedule(): array
    {
        return array_map(function (string $line): array {
            $parts = array_map('trim', explode('|', $line));

            return ['title' => $parts[0], 'time' => $parts[1] ?? '', 'day' => $parts[2] ?? ''];
        }, option_array('service_times'));
    }
}

if (!function_exists('whatsapp_url')) {
    function whatsapp_url(?string $text = null): ?string
    {
        $number = preg_replace('/\D+/', '', (string) option('whatsapp', ''));

        if (!$number) {
            return null;
        }

        $text ??= 'Hola ' . option('site_title', '') . ', me gustaría obtener más información.';

        return "https://wa.me/{$number}?text=" . rawurlencode($text);
    }
}

if (!function_exists('maps_url')) {
    function maps_url(): ?string
    {
        if ($url = option('maps_url')) {
            return $url;
        }

        $address = option('address');

        return $address ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address) : null;
    }
}

if (!function_exists('time_ago')) {
    function time_ago(?string $date): string
    {
        if (!$date || !($ts = strtotime($date))) {
            return '';
        }

        $diff = time() - $ts;

        return match (true) {
            $diff < 60 => 'hace un momento',
            $diff < 3600 => 'hace ' . intdiv($diff, 60) . ' min',
            $diff < 86400 => 'hace ' . intdiv($diff, 3600) . ' h',
            $diff < 172800 => 'ayer',
            $diff < 604800 => 'hace ' . intdiv($diff, 86400) . ' días',
            default => format_date($date),
        };
    }
}
