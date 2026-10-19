<?php
/**
 * Ver y arreglar el rol de las cuentas de personal.
 *
 * Sirve para salir del caso en que nadie puede entrar al panel: "Acceso
 * denegado." en /manager significa que la sesión es válida y la cuenta
 * está activa, pero su rol no tiene la capacidad 'panel.access'. Como el
 * panel es justo lo que no se puede abrir, el arreglo tiene que venir de
 * fuera.
 *
 * Uso (desde la raíz del proyecto):
 *   php scripts/roles.php                          ver cuentas y roles
 *   php scripts/roles.php correo@dominio admin     cambiar el rol
 *   php scripts/roles.php correo@dominio admin --activar
 *
 * Sin --activar no se toca el estado de la cuenta.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit("Sólo por línea de comandos.\n");
}

$raiz = dirname(__DIR__);
require $raiz . '/vendor/autoload.php';
Core\Env::load($raiz . '/.env');
Core\Config::load($raiz . '/config/config.php');

$db = Core\Database::getInstance();

$correo  = $argv[1] ?? null;
$rol     = $argv[2] ?? null;
$activar = in_array('--activar', $argv, true);

/** Roles que pueden abrir el panel, con lo que significa cada uno. */
$staff = [];
foreach (Core\Permissions::allRoles() as $r) {
    if (Core\Permissions::roleCan($r, 'panel.access')) {
        $staff[$r] = Core\Permissions::label($r);
    }
}

// ── Sólo informar ───────────────────────────────────────────────────
if ($correo === null) {
    echo "Roles que pueden entrar al panel:\n";
    foreach ($staff as $r => $etiqueta) {
        printf("   %-18s %s\n", $r, $etiqueta);
    }

    echo "\nCuentas que NO son de estudiante:\n";
    $filas = $db->fetchAll(
        "SELECT id, name, email, role, status FROM users WHERE role <> 'student' ORDER BY id"
    );
    if (!$filas) {
        echo "   (ninguna)\n";
    }
    foreach ($filas as $u) {
        $puede = isset($staff[$u['role']]) && $u['status'] === 'active';
        printf("   #%-5s %-24s %-32s %-18s %-10s %s\n",
            $u['id'],
            mb_strimwidth((string) $u['name'], 0, 22, '…'),
            mb_strimwidth((string) $u['email'], 0, 30, '…'),
            $u['role'],
            $u['status'],
            $puede ? 'entra' : '← NO ENTRA');
    }

    echo "\nPara arreglar una cuenta:\n";
    echo "   php scripts/roles.php correo@dominio admin --activar\n";
    exit;
}

// ── Cambiar el rol ──────────────────────────────────────────────────
if ($rol === null || !isset($staff[$rol])) {
    exit("Rol no válido. Usa uno de: " . implode(', ', array_keys($staff)) . "\n");
}

$u = $db->fetchOne('SELECT id, name, email, role, status FROM users WHERE email = ?', [$correo]);
if (!$u) {
    exit("No hay ninguna cuenta con el correo {$correo}.\n");
}

printf("Cuenta #%s %s <%s>\n", $u['id'], $u['name'], $u['email']);
printf("   rol    : %s → %s\n", $u['role'], $rol);
printf("   estado : %s%s\n", $u['status'], $activar && $u['status'] !== 'active' ? ' → active' : '');

$datos = ['role' => $rol];
if ($activar) {
    $datos['status'] = 'active';
}

if (!$db->update('users', $datos, 'id = :id', ['id' => $u['id']])) {
    exit("\nNo se pudo guardar. Revisa el log de errores de PHP.\n");
}

$v = $db->fetchOne('SELECT role, status FROM users WHERE id = ?', [$u['id']]);
printf("\nAhora: rol=%s estado=%s · %s\n", $v['role'], $v['status'],
    isset($staff[$v['role']]) && $v['status'] === 'active'
        ? 'esta cuenta ya puede entrar al panel'
        : 'OJO: sigue sin poder entrar');
