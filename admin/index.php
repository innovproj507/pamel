<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Core\Application;

// Create application
$app = new Application();
$router = $app->getRouter();

// Load web routes first (for shared routes like login, logout, etc.)
$webRoutes = require __DIR__ . '/../routes/web.php';
$webRoutes($router);

// Load admin routes
$adminRoutes = require __DIR__ . '/../routes/admin.php';
$adminRoutes($router);

// Run the application
$app->run();
