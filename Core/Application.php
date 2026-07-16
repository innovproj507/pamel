<?php

namespace Core;

class Application
{
    private $router;
    private $pluginManager;

    public function __construct()
    {
        // Load environment variables before config so $_ENV is available
        Env::load(__DIR__ . '/../.env');

        // Load configuration
        Config::load(__DIR__ . '/../config/config.php');

        // Set timezone
        date_default_timezone_set(Config::get('site.timezone', 'UTC'));

        // Never expose errors to the browser in production
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(E_ALL);
        ini_set('log_errors', 1);

        // Harden session cookies
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.use_strict_mode', 1);

        // Share session across subdomains (elearning.* ↔ main site)
        // Only on real production domains — skip localhost/.test/.local to avoid cookie issues
        $siteHost = parse_url(Config::get('site.url', ''), PHP_URL_HOST) ?? '';
        $devSuffixes = ['.test', '.local', '.dev', 'localhost'];
        $isLocalDev  = ($siteHost === 'localhost');
        foreach ($devSuffixes as $suffix) {
            if (substr($siteHost, -strlen($suffix)) === $suffix) {
                $isLocalDev = true;
                break;
            }
        }
        if ($siteHost && !$isLocalDev) {
            ini_set('session.cookie_domain', '.' . $siteHost);
        }

        // Initialize components
        $this->router = new Router();
        $this->pluginManager = PluginManager::getInstance();

        // Make app globally available for plugins
        $GLOBALS['app'] = $this;

        // Load plugins
        $this->pluginManager->loadPlugins();

        // Initialize language
        Language::init();
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    public function run()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        // Remove base path only when CMS lives in a subdirectory (e.g. /cms/)
        $basePath = rtrim(parse_url(Config::get('site.url'), PHP_URL_PATH) ?? '', '/');
        if ($basePath !== '' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = $uri ?: '/';

        $this->router->dispatch($uri, $method);
    }
}
