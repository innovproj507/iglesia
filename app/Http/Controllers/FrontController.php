<?php

namespace App\Http\Controllers;

use Cms\Content\PostRepository;
use Cms\Content\PostTypeRegistry;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Menus\MenuRepository;
use Cms\Themes\ThemeManager;
use Cms\Themes\View;

class FrontController
{
    public function __construct(
        private View $view,
        private PostRepository $posts,
        private PostTypeRegistry $postTypes,
        private ThemeManager $themes,
        private MenuRepository $menus,
    ) {
    }

    public function home(Request $request): Response
    {
        $template = $this->themes->resolveHome() ?? '404';

        $recentPosts = $this->posts->paginate('post', 1, 3, 'published')['items'];
        $recentSermons = $this->posts->paginate('sermon', 1, 3, 'published')['items'];

        return Response::html($this->view->render($template, array_merge($this->sharedData(), [
            'recentPosts' => $recentPosts,
            'recentSermons' => $recentSermons,
        ])));
    }

    public function archive(Request $request): Response
    {
        $type = 'post';
        $template = $this->themes->resolveArchive($type) ?? '404';
        $page = (int) $request->input('page', 1);
        $result = $this->posts->paginate($type, $page, 10, 'published');

        return Response::html($this->view->render($template, array_merge($this->sharedData(), [
            'posts' => $result['items'],
            'page' => $page,
        ])));
    }

    public function show(Request $request): Response
    {
        $slug = (string) $request->param('slug');

        $page = $this->posts->findBySlug('page', $slug);

        if ($page && $page['status'] === 'published') {
            return $this->renderSingle($page, 'page');
        }

        $post = $this->posts->findBySlug('post', $slug);

        if ($post && $post['status'] === 'published') {
            return $this->renderSingle($post, 'post');
        }

        return $this->notFound($request);
    }

    private function renderSingle(array $post, string $postType): Response
    {
        $templateName = $postType === 'page'
            ? $this->themes->resolvePage($post['slug'])
            : $this->themes->resolveSingle($postType, $post['slug']);

        $content = apply_filters('the_content', $post['content'] ?? '', $post);

        return Response::html($this->view->render($templateName ?? '404', array_merge($this->sharedData(), [
            'post' => array_merge($post, ['content' => $content]),
        ])));
    }

    public function notFound(Request $request): Response
    {
        $template = $this->themes->resolve404() ?? '404';

        return new Response($this->view->render($template, $this->sharedData()), 404);
    }

    private function sharedData(): array
    {
        $primaryMenu = $this->menus->findByLocation('primary');
        $footerMenu = $this->menus->findByLocation('footer');

        return [
            'siteTitle' => option('site_title', 'Iglesia CMS'),
            'tagline' => option('tagline', ''),
            'serviceTimes' => option_array('service_times'),
            'socialLinks' => option_array('social_links'),
            'primaryMenuItems' => $primaryMenu ? $this->menus->items((int) $primaryMenu['id']) : [],
            'footerMenuItems' => $footerMenu ? $this->menus->items((int) $footerMenu['id']) : [],
        ];
    }
}
