<?php

return [
    'database' => [
        'host'     => $_ENV['DB_HOST'] ?? 'localhost',
        'dbname'   => $_ENV['DB_NAME'] ?? 'cms_db2',
        'username' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASS'] ?? '',
        'charset'  => 'utf8mb4',
    ],

    'site' => [
        'name'      => $_ENV['SITE_NAME'] ?? 'My CMS',
        'url'       => $_ENV['SITE_URL']  ?? 'http://cms.test',
        'admin_url' => ($_ENV['SITE_URL'] ?? 'http://cms.test') . '/manager',
        'timezone'  => $_ENV['TIMEZONE']  ?? 'America/Panama',
    ],

    'security' => [
        'session_lifetime'    => 7200,
        'password_min_length' => 8,
    ],

    'email' => [
        'from_email'      => $_ENV['MAIL_FROM']       ?? 'info@pamel.edu.pa',
        'from_name'       => $_ENV['MAIL_FROM_NAME']  ?? 'PAMEL',
        'admin_email'     => $_ENV['MAIL_ADMIN']      ?? 'info@pamel.edu.pa',
        'smtp_enabled'    => filter_var($_ENV['SMTP_ENABLED'] ?? 'false', FILTER_VALIDATE_BOOLEAN),
        'smtp_host'       => $_ENV['SMTP_HOST']        ?? 'smtp.gmail.com',
        'smtp_port'       => (int) ($_ENV['SMTP_PORT'] ?? 587),
        'smtp_username'   => $_ENV['SMTP_USER']        ?? '',
        'smtp_password'   => $_ENV['SMTP_PASS']        ?? '',
        'smtp_encryption' => $_ENV['SMTP_ENCRYPTION']  ?? 'tls',
    ],

    'paths' => [
        'root'    => dirname(__DIR__),
        'plugins' => dirname(__DIR__) . '/plugins',
        'uploads' => dirname(__DIR__) . '/public/uploads',
        'cache'   => dirname(__DIR__) . '/cache',
    ],

    'plugins' => [
        'ecommerce',
        'seo',
        'paypal',
        'maintenance',
        'elearning',
    ],

    'app' => [
        'secret'   => $_ENV['APP_SECRET']    ?? 'pamel-api-secret-change-me-in-production',
        'sync_key' => $_ENV['APP_SYNC_KEY']  ?? 'PAMEL_SECRET_SYNC_2025',
    ],
];
