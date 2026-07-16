<?php
/**
 * Verificar si un usuario existe en la BD.
 * Uso: php scripts/check_user.php
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Config;
use Core\Database;

Config::load(__DIR__ . '/../config/config.php');
$db   = Database::getInstance();
$user = $db->fetchOne("SELECT email, role FROM users WHERE email = ?", ['estudiante@prueba.com']);

if ($user) {
    echo "Usuario encontrado: " . $user['email'] . " - Rol: " . $user['role'] . "\n";
} else {
    echo "Usuario NO encontrado.\n";
}
