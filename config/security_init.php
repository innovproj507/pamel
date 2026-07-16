<?php
/**
 * Archivo de inicialización de seguridad
 * Debe incluirse al inicio de cada request
 */

require_once __DIR__ . '/../Core/Security.php';

use Core\Security;

// Configurar sesiones seguras
Security::configureSecureSessions();

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Forzar HTTPS en producción (comentado por defecto)
// Security::forceHttps();

// Regenerar ID de sesión periódicamente para prevenir session fixation
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) { // Cada 5 minutos
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Headers de seguridad
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Content Security Policy (ajustar según necesidades)
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' https://cdnjs.cloudflare.com https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'");
