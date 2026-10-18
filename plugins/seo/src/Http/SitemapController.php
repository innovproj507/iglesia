<?php

namespace Plugins\Seo\Http;

use Cms\Database\Connection;
use Cms\Http\Request;
use Cms\Http\Response;
use Plugins\Seo\SeoHead;

class SitemapController
{
    public function __construct(private Connection $connection)
    {
    }

    public function sitemap(Request $request): Response
    {
        $base = SeoHead::baseUrl();
        $urls = [['loc' => $base . '/', 'lastmod' => null, 'priority' => '1.0']];

        $rows = $this->connection->pdo()->query(
            "SELECT p.post_type, p.slug, p.updated_at FROM posts p
             LEFT JOIN postmeta m ON m.post_id = p.id AND m.meta_key = 'seo_noindex'
             WHERE p.status = 'published' AND COALESCE(m.meta_value, '0') <> '1'
             ORDER BY p.post_type, p.updated_at DESC"
        )->fetchAll(\PDO::FETCH_ASSOC);

        $types = array_count_values(array_column($rows, 'post_type'));

        if (!empty($types['post'])) {
            $urls[] = ['loc' => $base . '/blog', 'lastmod' => null, 'priority' => '0.7'];
        }

        if (!empty($types['sermon'])) {
            $urls[] = ['loc' => $base . '/sermones', 'lastmod' => null, 'priority' => '0.8'];
        }

        foreach ($rows as $row) {
            $path = match ($row['post_type']) {
                'sermon' => '/sermones/' . $row['slug'],
                'page', 'post' => '/' . $row['slug'],
                default => null,
            };

            if ($path) {
                $urls[] = ['loc' => $base . $path, 'lastmod' => $row['updated_at'], 'priority' => $row['post_type'] === 'page' ? '0.8' : '0.6'];
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= '  <url><loc>' . htmlspecialchars($url['loc'], ENT_XML1) . '</loc>'
                . ($url['lastmod'] ? '<lastmod>' . date('Y-m-d', strtotime($url['lastmod'])) . '</lastmod>' : '')
                . '<priority>' . $url['priority'] . '</priority></url>' . "\n";
        }

        $xml .= '</urlset>' . "\n";

        return new Response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    public function robots(Request $request): Response
    {
        $lines = ['User-agent: *'];

        if (option('seo_noindex')) {
            $lines[] = 'Disallow: /';
        } else {
            $lines[] = 'Disallow: /admin';
            $lines[] = 'Disallow: /install';
        }

        $extra = trim((string) option('seo_robots_extra', ''));

        if ($extra !== '') {
            $lines[] = $extra;
        }

        $lines[] = '';
        $lines[] = 'Sitemap: ' . SeoHead::baseUrl() . '/sitemap.xml';

        return new Response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
