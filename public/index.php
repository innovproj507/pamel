<?php

ob_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/Controllers/AuthController.php';
require_once BASE_PATH . '/Controllers/AccountController.php';

use Core\Application;

// CORS for API routes — allow elearning subdomain to call this API
if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
    $allowedOrigins = [
        'https://elearning.pamel.edu.pa',
        'http://elearning.test',
        'http://elearning.pamel.edu.pa',
    ];
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (in_array($origin, $allowedOrigins, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 3600');
    }
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

$app    = new Application();
$router = $app->getRouter();

$host  = $_SERVER['HTTP_HOST'] ?? '';
$isLms = strpos($host, 'elearning.') !== false;
define('IS_LMS', $isLms);

if (IS_LMS) {
    $lmsFile = BASE_PATH . '/routes/lms.php';
    if (file_exists($lmsFile)) {
        $lmsRoutes = require $lmsFile;
        $lmsRoutes($router);
    }
} else {
    $webRoutes = require BASE_PATH . '/routes/web.php';
    $webRoutes($router);

    $adminRoutes = require BASE_PATH . '/routes/admin.php';
    $adminRoutes($router);

    $apiFile = BASE_PATH . '/routes/api.php';
    if (file_exists($apiFile)) {
        $apiRoutes = require $apiFile;
        $apiRoutes($router);
    }
}

$app->run();
