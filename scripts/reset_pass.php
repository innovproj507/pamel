<?php
/**
 * Resetear la contraseña de un usuario.
 * Uso: php scripts/reset_pass.php [email] [nueva_contraseña]
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Config;
use Core\Database;

Config::load(__DIR__ . '/../config/config.php');
$db    = Database::getInstance();
$email = $argv[1] ?? 'student@pamel.com';
$pass  = $argv[2] ?? 'password123';
$hash  = password_hash($pass, PASSWORD_BCRYPT);

$db->query("UPDATE users SET password = ? WHERE email = ?", [$hash, $email]);
echo "Contraseña actualizada para: $email\n";
