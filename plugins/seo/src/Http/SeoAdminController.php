<?php

namespace Plugins\Seo\Http;

use Cms\Admin\AdminMenu;
use Cms\Admin\Controllers\AdminController;
use Cms\Auth\AuthManager;
use Cms\Database\Connection;
use Cms\Http\Request;
use Cms\Http\Response;
use Cms\Settings\OptionsRepository;
use Cms\Themes\View;
use Plugins\Seo\SeoHead;

class SeoAdminController extends AdminController
{
    private const FIELDS = [
        'seo_title_format', 'seo_default_description', 'seo_default_image',
        'seo_google_verification', 'seo_bing_verification', 'seo_ga_id', 'seo_robots_extra',
    ];

    public function __construct(
        View $view,
        AuthManager $auth,
        AdminMenu $adminMenu,
        private OptionsRepository $options,
        private Connection $connection,
    ) {
        parent::__construct($view, $auth, $adminMenu);
    }

    public function index(Request $request): Response
    {
        $values = [];

        foreach (self::FIELDS as $field) {
            $values[$field] = (string) $this->options->get($field, '');
        }

        $values['seo_noindex'] = (bool) $this->options->get('seo_noindex', false);

        // Contenido publicado sin descripción propia ni extracto
        $missing = $this->connection->pdo()->query(
            "SELECT p.id, p.title, p.post_type FROM posts p
             LEFT JOIN postmeta m ON m.post_id = p.id AND m.meta_key = 'seo_description'
             WHERE p.status = 'published' AND COALESCE(m.meta_value, '') = '' AND COALESCE(p.excerpt, '') = ''
             ORDER BY p.updated_at DESC LIMIT 10"
        )->fetchAll(\PDO::FETCH_ASSOC);

        $checks = [
            ['Descripción del sitio', $values['seo_default_description'] !== '', 'Es el texto que Google muestra bajo el nombre de la iglesia.'],
            ['Imagen para compartir', ($values['seo_default_image'] ?: option('hero_image')) !== null && ($values['seo_default_image'] ?: option('hero_image')) !== '', 'Aparece al compartir enlaces en WhatsApp y Facebook.'],
            ['Sitio visible en buscadores', !$values['seo_noindex'], 'Si está oculto, Google no mostrará la página.'],
            ['Verificación de Google', $values['seo_google_verification'] !== '', 'Necesaria para usar Google Search Console.'],
            ['Google Analytics', (bool) preg_match('/^G-[A-Z0-9]+$/i', $values['seo_ga_id']), 'Para saber cuántas personas visitan el sitio.'],
            ['Contenido con descripción', !$missing, $missing ? count($missing) . ' publicación(es) sin descripción.' : 'Todas las publicaciones tienen descripción.'],
            ['Dirección y teléfono', option('address') && option('phone'), 'Ayudan a aparecer en búsquedas locales.'],
        ];

        $score = (int) round(count(array_filter(array_column($checks, 1))) / count($checks) * 100);
        $home = SeoHead::for(['post' => null, 'pageTitle' => null, 'siteTitle' => option('site_title', ''), 'tagline' => option('tagline', '')]);

        return $this->render('seo::admin/settings', [
            'title' => 'SEO',
            'values' => $values,
            'checks' => $checks,
            'score' => $score,
            'missing' => $missing,
            'baseUrl' => SeoHead::baseUrl(),
            'home' => $home,
            'flash' => $request->input('saved') ? 'Ajustes de SEO guardados.' : null,
        ], 'seo');
    }

    public function update(Request $request): Response
    {
        foreach (self::FIELDS as $field) {
            $this->options->set($field, trim((string) $request->input($field, '')));
        }

        $this->options->set('seo_noindex', $request->input('seo_noindex') ? '1' : '0');

        return Response::redirect('/admin/seo?saved=1');
    }
}
