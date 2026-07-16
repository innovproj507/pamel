<?php
/**
 * SEO Plugin
 * Provides SEO functionality including meta tags, sitemaps, and robots.txt
 */

use Core\PluginManager;
use Core\Config;

// Get plugin manager instance
$pm = PluginManager::getInstance();
$router = $GLOBALS['app']->getRouter();

// Register SEO routes
$router->get('/sitemap.xml', 'Plugins\Seo\Controllers\SeoController@sitemap', 'sitemap');
$router->get('/robots.txt', 'Plugins\Seo\Controllers\SeoController@robots', 'robots');

// Admin routes
$router->get('/manager/seo', 'Plugins\Seo\Controllers\AdminSeoController@index', 'admin.seo');
$router->post('/manager/seo/meta', 'Plugins\Seo\Controllers\AdminSeoController@saveMeta', 'admin.seo.meta');
$router->post('/manager/seo/robots', 'Plugins\Seo\Controllers\AdminSeoController@saveRobots', 'admin.seo.robots');

// Add hook to inject meta tags in header
$pm->addAction('header_meta', function() {
    $currentUrl = $_SERVER['REQUEST_URI'];
    $db = \Core\Database::getInstance();
    
    $metaTags = $db->fetchOne("SELECT * FROM meta_tags WHERE page_url = ?", [$currentUrl]);
    
    if ($metaTags) {
        echo '<meta name="description" content="' . htmlspecialchars($metaTags['description']) . '">' . "\n";
        if ($metaTags['keywords']) {
            echo '<meta name="keywords" content="' . htmlspecialchars($metaTags['keywords']) . '">' . "\n";
        }
        if ($metaTags['og_title']) {
            echo '<meta property="og:title" content="' . htmlspecialchars($metaTags['og_title']) . '">' . "\n";
        }
        if ($metaTags['og_description']) {
            echo '<meta property="og:description" content="' . htmlspecialchars($metaTags['og_description']) . '">' . "\n";
        }
        if ($metaTags['og_image']) {
            echo '<meta property="og:image" content="' . htmlspecialchars($metaTags['og_image']) . '">' . "\n";
        }
    }
});
