<?php

namespace Plugins\Seo;

use Cms\Admin\AdminMenu;
use Cms\Content\PostRepository;
use Cms\Hooks\HookManager;
use Cms\Http\Middleware\AuthMiddleware;
use Cms\Http\Middleware\CapabilityMiddleware;
use Cms\Http\Middleware\CsrfMiddleware;
use Cms\Http\Request;
use Cms\Http\Router;
use Cms\Plugins\PluginInterface;
use Plugins\Seo\Http\SeoAdminController;
use Plugins\Seo\Http\SitemapController;

class Seo implements PluginInterface
{
    public function register(HookManager $hooks): void
    {
        $hooks->addAction('router.routes', function (Router $router) {
            $router->get('/sitemap.xml', [SitemapController::class, 'sitemap']);
            $router->get('/robots.txt', [SitemapController::class, 'robots']);

            $router->group('/admin', function (Router $router) {
                $router->get('/seo', [SeoAdminController::class, 'index'], [[CapabilityMiddleware::class, 'manage_options']]);
                $router->post('/seo', [SeoAdminController::class, 'update'], [CsrfMiddleware::class, [CapabilityMiddleware::class, 'manage_options']]);
            }, [AuthMiddleware::class]);
        });

        $hooks->addAction('admin.menu', function (AdminMenu $menu) {
            $menu->register('seo', 'SEO', 'search', 'manage_options', '/admin/seo', null, 75);
        });

        // Título y descripción del documento
        $hooks->addFilter('document_title', fn (string $title, array $context) => SeoHead::for($context)->title ?: $title, 10, 2);
        $hooks->addFilter('document_description', fn (string $description, array $context) => SeoHead::for($context)->description ?: $description, 10, 2);

        // Etiquetas sociales, canónica, datos estructurados y analítica
        $hooks->addAction('front.head', function (array $context = []) {
            echo SeoHead::for($context)->render();
        }, 10, 1);

        // Caja SEO en el editor de cualquier contenido
        $hooks->addAction('admin.post.edit.meta_boxes', function (array $post) {
            echo view('seo::admin/meta-box', [
                'post' => $post,
                'meta' => app(PostRepository::class)->allMeta((int) $post['id']),
            ]);
        }, 20, 1);

        $hooks->addAction('admin.post.saved', function (int $postId, string $postType, Request $request) {
            $posts = app(PostRepository::class);
            $posts->setMeta($postId, 'seo_title', trim((string) $request->input('seo_title', '')));
            $posts->setMeta($postId, 'seo_description', trim((string) $request->input('seo_description', '')));
            $posts->setMeta($postId, 'seo_image', trim((string) $request->input('seo_image', '')));
            $posts->setMeta($postId, 'seo_noindex', $request->input('seo_noindex') ? '1' : '0');
        }, 20, 3);
    }

    public function activate(): void
    {
        // Valores iniciales sin pisar lo que ya exista
        foreach ([
            'seo_title_format' => '{title} | {site}',
            'seo_default_description' => option('tagline', ''),
        ] as $key => $value) {
            if (option($key) === null) {
                update_option($key, $value);
            }
        }
    }

    public function deactivate(): void
    {
    }

    public function uninstall(): void
    {
    }
}
