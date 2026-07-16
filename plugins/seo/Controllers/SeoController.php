<?php

namespace Plugins\Seo\Controllers;

use Core\Controller;
use Plugins\Seo\Models\MetaTag;

class SeoController extends Controller
{
    private $metaTagModel;

    public function __construct()
    {
        parent::__construct();
        $this->metaTagModel = new MetaTag();
    }

    public function sitemap()
    {
        header('Content-Type: application/xml; charset=utf-8');

        $baseUrl = \Core\Config::get('site.url');
        
        // Get all pages
        $pages = $this->db->fetchAll("SELECT slug, updated_at FROM pages WHERE status = 'published'");
        $products = $this->db->fetchAll("SELECT slug, updated_at FROM products WHERE status = 'active'");

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        echo "  <url>\n";
        echo "    <loc>{$baseUrl}/</loc>\n";
        echo "    <changefreq>daily</changefreq>\n";
        echo "    <priority>1.0</priority>\n";
        echo "  </url>\n";

        // Pages
        foreach ($pages as $page) {
            echo "  <url>\n";
            echo "    <loc>{$baseUrl}/page/{$page['slug']}</loc>\n";
            echo "    <lastmod>" . date('Y-m-d', strtotime($page['updated_at'])) . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.8</priority>\n";
            echo "  </url>\n";
        }

        // Products
        foreach ($products as $product) {
            echo "  <url>\n";
            echo "    <loc>{$baseUrl}/shop/{$product['slug']}</loc>\n";
            echo "    <lastmod>" . date('Y-m-d', strtotime($product['updated_at'])) . "</lastmod>\n";
            echo "    <changefreq>weekly</changefreq>\n";
            echo "    <priority>0.7</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }

    public function robots()
    {
        header('Content-Type: text/plain; charset=utf-8');

        $baseUrl = \Core\Config::get('site.url');

        // Get custom robots.txt from settings
        $robotsTxt = $this->db->fetchOne("SELECT setting_value FROM settings WHERE setting_key = 'robots_txt'");

        if ($robotsTxt && !empty($robotsTxt['setting_value'])) {
            echo $robotsTxt['setting_value'];
        } else {
            // Default robots.txt
            echo "User-agent: *\n";
            echo "Allow: /\n";
            echo "Disallow: /admin/\n";
            echo "\n";
            echo "Sitemap: {$baseUrl}/sitemap.xml\n";
        }
        exit;
    }
}
