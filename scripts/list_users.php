<?php
/**
 * Listar todos los usuarios de la BD.
 * Uso: php scripts/list_users.php
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Config;
use Core\Database;

Config::load(__DIR__ . '/../config/config.php');
$db    = Database::getInstance();
$users = $db->fetchAll("SELECT id, email, role FROM users");

echo "Total usuarios: " . count($users) . "\n";
foreach ($users as $u) {
    echo "- [{$u['id']}] {$u['email']} ({$u['role']})\n";
}
