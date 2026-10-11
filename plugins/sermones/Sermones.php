<?php

namespace Plugins\Sermones;

use Cms\Admin\AdminMenu;
use Cms\Content\PostTypeRegistry;
use Cms\Content\TermRepository;
use Cms\Hooks\HookManager;
use Cms\Http\Request;
use Cms\Http\Router;
use Cms\Plugins\PluginInterface;
use Plugins\Sermones\Http\SermonController;

class Sermones implements PluginInterface
{
    public function register(HookManager $hooks): void
    {
        $hooks->addAction('cms.post_types.register', function (PostTypeRegistry $registry) {
            $registry->register('sermon', [
                'label' => 'Sermones',
                'label_singular' => 'Sermón',
                'has_archive' => true,
                'supports' => ['title', 'editor', 'thumbnail'],
                'menu_icon' => 'mic',
            ]);
        });

        $hooks->addAction('router.routes', function (Router $router) {
            $router->get('/sermones', [SermonController::class, 'archive']);
            $router->get('/sermones/{slug}', [SermonController::class, 'show']);
        });

        $hooks->addAction('admin.menu', function (AdminMenu $menu) {
            $menu->register('sermones', 'Sermones', 'mic', 'edit_posts', '/admin/posts?type=sermon', null, 15);
        });

        $hooks->addAction('admin.post.edit.meta_boxes', function (array $post) {
            if ($post['post_type'] !== 'sermon') {
                return;
            }

            echo view('sermones::admin/meta-box', ['post' => $post]);
        }, 10, 1);

        $hooks->addAction('admin.post.saved', function (int $postId, string $postType, Request $request) {
            if ($postType !== 'sermon') {
                return;
            }

            $posts = app(\Cms\Content\PostRepository::class);
            $posts->setMeta($postId, 'predicador', (string) $request->input('predicador', ''));
            $posts->setMeta($postId, 'audio_url', (string) $request->input('audio_url', ''));
            $posts->setMeta($postId, 'video_url', (string) $request->input('video_url', ''));
            $posts->setMeta($postId, 'fecha_servicio', (string) $request->input('fecha_servicio', ''));

            $serieName = trim((string) $request->input('serie', ''));

            if ($serieName !== '') {
                $terms = app(TermRepository::class);
                $term = $terms->findOrCreate('sermon_series', $serieName);
                $terms->attach($postId, (int) $term['id']);
            }
        }, 10, 3);
    }

    public function activate(): void
    {
        // No dedicated tables: sermons reuse posts/postmeta/terms from the core.
    }

    public function deactivate(): void
    {
    }

    public function uninstall(): void
    {
    }
}
