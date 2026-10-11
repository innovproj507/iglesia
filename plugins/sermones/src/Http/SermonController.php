<?php

namespace Plugins\Sermones\Http;

use Cms\Content\PostRepository;
use Cms\Content\TermRepository;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Menus\MenuRepository;
use Cms\Themes\ThemeManager;
use Cms\Themes\View;

class SermonController
{
    public function __construct(
        private View $view,
        private PostRepository $posts,
        private TermRepository $terms,
        private ThemeManager $themes,
        private MenuRepository $menus,
    ) {
    }

    public function archive(Request $request): Response
    {
        $page = (int) $request->input('page', 1);
        $result = $this->posts->paginate('sermon', $page, 12, 'published');

        $sermons = array_map(fn ($post) => $this->presentSermon($post), $result['items']);

        $template = $this->themes->resolveArchive('sermon') ?? 'sermones::archive-sermon';

        return Response::html($this->view->render($template, array_merge($this->sharedData(), [
            'sermons' => $sermons,
        ])));
    }

    public function show(Request $request): Response
    {
        $slug = (string) $request->param('slug');
        $post = $this->posts->findBySlug('sermon', $slug);

        if (!$post || $post['status'] !== 'published') {
            return Response::notFound();
        }

        $template = $this->themes->resolveSingle('sermon', $slug) ?? 'sermones::single-sermon';

        return Response::html($this->view->render($template, array_merge($this->sharedData(), [
            'sermon' => $this->presentSermon($post),
        ])));
    }

    private function presentSermon(array $post): array
    {
        $meta = $this->posts->allMeta((int) $post['id']);
        $series = $this->terms->termsForPost((int) $post['id'], 'sermon_series');

        return array_merge($post, [
            'predicador' => $meta['predicador'] ?? '',
            'audio_url' => $meta['audio_url'] ?? '',
            'video_url' => $meta['video_url'] ?? '',
            'fecha_servicio' => $meta['fecha_servicio'] ?? '',
            'series' => $series,
        ]);
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
