<?php

namespace Plugins\Mantenimiento;

use Cms\Admin\AdminMenu;
use Cms\Auth\AuthManager;
use Cms\Hooks\HookManager;
use Cms\Http\Middleware\AuthMiddleware;
use Cms\Http\Middleware\CapabilityMiddleware;
use Cms\Http\Middleware\CsrfMiddleware;
use Cms\Http\Response;
use Cms\Http\Router;
use Cms\Plugins\PluginInterface;
use Plugins\Mantenimiento\Http\MaintenanceController;

class Mantenimiento implements PluginInterface
{
    public const COOKIE = 'cms_maint_access';

    /** Rutas que siguen funcionando durante el mantenimiento. */
    private const ALLOWED_PREFIXES = ['/admin', '/install', '/assets/', '/uploads/'];

    public function register(HookManager $hooks): void
    {
        $hooks->addAction('router.routes', function (Router $router) {
            $router->group('/admin', function (Router $router) {
                $cap = [CapabilityMiddleware::class, 'manage_options'];
                $router->get('/mantenimiento', [MaintenanceController::class, 'index'], [$cap]);
                $router->post('/mantenimiento', [MaintenanceController::class, 'update'], [CsrfMiddleware::class, $cap]);
                $router->post('/mantenimiento/toggle', [MaintenanceController::class, 'toggle'], [CsrfMiddleware::class, $cap]);
                $router->post('/mantenimiento/token', [MaintenanceController::class, 'regenerateToken'], [CsrfMiddleware::class, $cap]);
                $router->get('/mantenimiento/vista-previa', [MaintenanceController::class, 'preview'], [$cap]);
            }, [AuthMiddleware::class]);
        });

        $hooks->addAction('admin.menu', function (AdminMenu $menu) {
            $menu->register('mantenimiento', 'Mantenimiento', 'wrench', 'manage_options', '/admin/mantenimiento', null, 85);
        });

        // Intercepta las visitas públicas antes de enrutar
        $hooks->addAction('cms.boot', fn () => $this->guard());

        // Aviso en el panel mientras está activo
        $hooks->addAction('admin.notices', function () {
            if (self::enabled()) {
                echo view('mantenimiento::admin/notice');
            }
        });

        // Etiqueta flotante para quien ve el sitio con acceso durante el mantenimiento
        $hooks->addAction('front.footer', function () {
            if (self::enabled()) {
                echo '<a href="/admin/mantenimiento" style="position:fixed;left:20px;bottom:20px;z-index:60;display:flex;align-items:center;gap:8px;'
                    . 'padding:10px 16px;border-radius:999px;background:#f59e0b;color:#1f2937;font:600 13px/1 system-ui,sans-serif;'
                    . 'box-shadow:0 10px 25px -5px rgba(0,0,0,.25);text-decoration:none">🛠️ Modo mantenimiento activo · los visitantes no ven el sitio</a>';
            }
        });
    }

    public static function enabled(): bool
    {
        return (bool) option('maint_enabled');
    }

    public static function token(): string
    {
        $token = (string) option('maint_token', '');

        if ($token === '') {
            $token = 'mt' . bin2hex(random_bytes(12));
            update_option('maint_token', $token);
        }

        return $token;
    }

    private function guard(): void
    {
        if (!self::enabled()) {
            return;
        }

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        foreach (self::ALLOWED_PREFIXES as $prefix) {
            if ($path === rtrim($prefix, '/') || str_starts_with($path, $prefix)) {
                return;
            }
        }

        // Enlace de acceso para compartir con el equipo: /?acceso=TOKEN
        $token = self::token();

        if (isset($_GET['acceso']) && hash_equals($token, (string) $_GET['acceso'])) {
            setcookie(self::COOKIE, hash('sha256', $token), ['expires' => time() + 7 * 86400, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
            Response::redirect($path)->send();
            exit;
        }

        $hasCookie = hash_equals(hash('sha256', $token), (string) ($_COOKIE[self::COOKIE] ?? ''));

        if ($hasCookie || app(AuthManager::class)->check()) {
            return;
        }

        self::page(503)->send();
        exit;
    }

    public static function page(int $status = 503): Response
    {
        $until = (string) option('maint_until', '');
        $untilTs = $until !== '' ? strtotime($until) : false;
        $retryAfter = $untilTs && $untilTs > time() ? $untilTs - time() : 3600;

        $html = view('mantenimiento::maintenance', [
            'siteTitle' => option('site_title', ''),
            'logo' => option('logo'),
            'image' => option('hero_image'),
            'title' => option('maint_title') ?: 'Estamos en mantenimiento',
            'message' => option('maint_message') ?: 'Estamos mejorando nuestro sitio para servirte mejor. Volveremos muy pronto.',
            'until' => $untilTs && $untilTs > time() ? date('c', $untilTs) : null,
            'showContact' => option('maint_show_contact') === null ? true : (bool) option('maint_show_contact'),
            'socialLinks' => option_array('social_links'),
        ]);

        return new Response($html, $status, [
            'Retry-After' => (string) $retryAfter,
            'Cache-Control' => 'no-store',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    public function activate(): void
    {
        self::token();
    }

    public function deactivate(): void
    {
        // Al desactivar el plugin, el sitio vuelve a estar visible.
        update_option('maint_enabled', '0');
    }

    public function uninstall(): void
    {
    }
}
