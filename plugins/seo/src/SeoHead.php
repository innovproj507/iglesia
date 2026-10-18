<?php

namespace Plugins\Seo;

use Cms\Auth\AuthManager;
use Cms\Content\PostRepository;

/**
 * Calcula las etiquetas SEO de la página actual a partir del contexto que entrega el tema.
 */
class SeoHead
{
    private static array $cache = [];

    public string $title = '';
    public string $description = '';
    public ?string $image = null;
    public string $canonical = '';
    public bool $noindex = false;
    public string $type = 'website';
    private ?array $post = null;
    private array $context = [];

    public static function for(array $context): self
    {
        $key = md5(serialize([$context['post']['id'] ?? null, $context['pageTitle'] ?? null, $_SERVER['REQUEST_URI'] ?? '']));

        return self::$cache[$key] ??= (new self())->build($context);
    }

    public static function baseUrl(): string
    {
        $https = ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? parse_url((string) ($_ENV['APP_URL'] ?? ''), PHP_URL_HOST) ?? 'localhost';

        return ($https ? 'https' : 'http') . '://' . $host;
    }

    public static function absolute(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        return preg_match('~^https?://~', $url) ? $url : self::baseUrl() . '/' . ltrim($url, '/');
    }

    private function build(array $context): self
    {
        $this->context = $context;
        $this->post = $context['post'] ?? null;
        $site = $context['siteTitle'] ?: option('site_title', '');
        $meta = $this->post && !empty($this->post['id']) ? app(PostRepository::class)->allMeta((int) $this->post['id']) : [];

        // Título
        $format = option('seo_title_format') ?: '{title} | {site}';
        $baseTitle = $context['pageTitle'] ?? null;

        if (!empty($meta['seo_title'])) {
            $this->title = $meta['seo_title'];
        } elseif ($baseTitle) {
            $this->title = strtr($format, ['{title}' => $baseTitle, '{site}' => $site]);
        } else {
            $tagline = $context['tagline'] ?: option('tagline', '');
            $this->title = $tagline ? "{$site} | {$tagline}" : $site;
        }

        // Descripción
        $this->description = $this->clip(
            ($meta['seo_description'] ?? '')
            ?: ($this->post['excerpt'] ?? '')
            ?: strip_tags((string) ($this->post['content'] ?? ''))
            ?: (string) option('seo_default_description', '')
            ?: (string) ($context['tagline'] ?? '')
        );

        // Imagen para redes sociales
        $this->image = self::absolute(
            ($meta['seo_image'] ?? '')
            ?: ($this->post ? post_image($this->post) : null)
            ?: option('seo_default_image')
            ?: option('hero_image')
            ?: option('logo')
        );

        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $this->canonical = self::baseUrl() . $path;
        $this->noindex = (bool) option('seo_noindex') || ($meta['seo_noindex'] ?? '0') === '1';
        $this->type = $this->post && in_array($this->post['post_type'] ?? '', ['post', 'sermon'], true) ? 'article' : 'website';

        return $this;
    }

    private function clip(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', html_entity_decode($text, ENT_QUOTES, 'UTF-8')));

        return mb_strlen($text) > 160 ? rtrim(mb_substr($text, 0, 157)) . '…' : $text;
    }

    public function render(): string
    {
        $e = fn (?string $v) => htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
        $site = option('site_title', '');
        $tags = [];

        $tags[] = '<link rel="canonical" href="' . $e($this->canonical) . '">';
        $tags[] = '<meta name="robots" content="' . ($this->noindex ? 'noindex, nofollow' : 'index, follow, max-image-preview:large') . '">';

        foreach ([
            'og:type' => $this->type,
            'og:site_name' => $site,
            'og:locale' => 'es_PA',
            'og:title' => $this->title,
            'og:description' => $this->description,
            'og:url' => $this->canonical,
            'og:image' => $this->image,
        ] as $property => $content) {
            if ($content) {
                $tags[] = '<meta property="' . $property . '" content="' . $e($content) . '">';
            }
        }

        $tags[] = '<meta name="twitter:card" content="' . ($this->image ? 'summary_large_image' : 'summary') . '">';
        $tags[] = '<meta name="twitter:title" content="' . $e($this->title) . '">';
        $tags[] = '<meta name="twitter:description" content="' . $e($this->description) . '">';

        if ($this->image) {
            $tags[] = '<meta name="twitter:image" content="' . $e($this->image) . '">';
        }

        if ($code = option('seo_google_verification')) {
            $tags[] = '<meta name="google-site-verification" content="' . $e($code) . '">';
        }

        if ($code = option('seo_bing_verification')) {
            $tags[] = '<meta name="msvalidate.01" content="' . $e($code) . '">';
        }

        $tags[] = '<script type="application/ld+json">' . json_encode($this->schema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';

        // Google Analytics 4 (no cuenta las visitas de quienes administran el sitio)
        $gaId = (string) option('seo_ga_id', '');

        if (preg_match('/^G-[A-Z0-9]+$/i', $gaId) && !app(AuthManager::class)->check()) {
            $tags[] = '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $e($gaId) . '"></script>';
            $tags[] = "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','" . $e($gaId) . "');</script>";
        }

        return "\n    " . implode("\n    ", $tags) . "\n";
    }

    /** Datos estructurados schema.org: la iglesia y, si aplica, el artículo actual. */
    private function schema(): array
    {
        $base = self::baseUrl();
        $church = array_filter([
            '@type' => 'Church',
            '@id' => $base . '/#iglesia',
            'name' => option('site_title', ''),
            'description' => option('seo_default_description') ?: option('tagline'),
            'url' => $base . '/',
            'logo' => self::absolute(option('logo')),
            'image' => self::absolute(option('hero_image')),
            'telephone' => option('phone'),
            'email' => option('email'),
            'address' => option('address') ? ['@type' => 'PostalAddress', 'streetAddress' => option('address'), 'addressCountry' => 'PA'] : null,
            'sameAs' => array_values(array_filter(option_array('social_links'))) ?: null,
        ]);

        $graph = [$church, ['@type' => 'WebSite', '@id' => $base . '/#sitio', 'url' => $base . '/', 'name' => option('site_title', ''), 'publisher' => ['@id' => $base . '/#iglesia'], 'inLanguage' => 'es']];

        if ($this->type === 'article' && $this->post) {
            $graph[] = array_filter([
                '@type' => 'Article',
                'headline' => $this->post['title'] ?? '',
                'description' => $this->description,
                'image' => $this->image,
                'datePublished' => !empty($this->post['published_at']) ? date('c', strtotime($this->post['published_at'])) : null,
                'dateModified' => !empty($this->post['updated_at']) ? date('c', strtotime($this->post['updated_at'])) : null,
                'author' => !empty($this->post['predicador']) ? ['@type' => 'Person', 'name' => $this->post['predicador']] : ['@id' => $base . '/#iglesia'],
                'publisher' => ['@id' => $base . '/#iglesia'],
                'mainEntityOfPage' => $this->canonical,
            ]);
        }

        return ['@context' => 'https://schema.org', '@graph' => $graph];
    }
}
