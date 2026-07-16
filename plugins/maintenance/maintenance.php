<?php
/**
 * Maintenance Plugin
 * Displays a branded maintenance page when the site is under maintenance.
 * Admin panel (/admin/*) is always accessible so the mode can be toggled.
 * Flag file: cache/maintenance.flag
 */

use Core\Config;

// Explicit require so the class loads on case-sensitive Linux filesystems
// without depending solely on the PSR-4 autoloader mapping.
require_once __DIR__ . '/Controllers/AdminMaintenanceController.php';

$router = $GLOBALS['app']->getRouter();

// ── Maintenance gate ────────────────────────────────────────────────────────
$flagFile   = Config::get('paths.cache') . '/maintenance.flag';
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$basePath   = parse_url(Config::get('site.url'), PHP_URL_PATH) ?? '';
$localUri   = $basePath ? substr($requestUri, strlen($basePath)) : $requestUri;
$localUri   = '/' . ltrim($localUri ?: '/', '/');

$isAdminRequest = str_starts_with($localUri, '/manager');
$isAdminUser    = \Core\Auth::getInstance()->isAdmin();   // logged-in admin bypasses the page

if (file_exists($flagFile) && !$isAdminRequest && !$isAdminUser) {
    $maintenanceData = json_decode(file_get_contents($flagFile), true) ?? [];
    $message         = $maintenanceData['message']    ?? 'We are performing scheduled maintenance.';
    $returnTime      = $maintenanceData['return_time'] ?? '';

    http_response_code(503);
    header('Retry-After: 3600');
    include __DIR__ . '/views/maintenance.php';
    exit;
}

// ── Admin routes ─────────────────────────────────────────────────────────────
$router->get(
    '/manager/maintenance',
    'Plugins\Maintenance\Controllers\AdminMaintenanceController@index',
    'admin.maintenance'
);

$router->post(
    '/manager/maintenance/toggle',
    'Plugins\Maintenance\Controllers\AdminMaintenanceController@toggle',
    'admin.maintenance.toggle'
);

$router->post(
    '/manager/maintenance/save',
    'Plugins\Maintenance\Controllers\AdminMaintenanceController@save',
    'admin.maintenance.save'
);
