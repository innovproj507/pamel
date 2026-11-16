<?php
/**
 * Rellena los huecos de los cursos LMS con los datos de su producto y lista
 * las diferencias que hay que revisar a mano.
 *
 *   php scripts/sync_cursos.php           # sólo muestra qué haría
 *   php scripts/sync_cursos.php --apply   # escribe los cambios
 *
 * Reglas en Plugins\Ecommerce\Services\CourseSync.
 */
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';
\Core\Env::load(BASE_PATH . '/.env');
\Core\Config::load(BASE_PATH . '/config/config.php');

$sync   = new \Plugins\Ecommerce\Services\CourseSync();
$plan   = $sync->planificar();
$aplicar = in_array('--apply', $argv, true);

$porCampo = [];
foreach ($plan['cambios'] as $c) {
    foreach ($c['campos'] as $campo => $_) {
        $porCampo[$campo] = ($porCampo[$campo] ?? 0) + 1;
    }
}
echo "Cursos con huecos que se rellenarían: " . count($plan['cambios']) . "\n";
foreach ($porCampo as $campo => $n) {
    echo "  - $campo: $n\n";
}

echo "\nPara revisar a mano (no se tocan):\n";
foreach ($plan['avisos'] as $a) {
    echo sprintf("  curso %d  %-7s producto=\"%s\"  curso=\"%s\"\n", $a['course_id'], $a['tipo'], $a['producto'], $a['curso']);
}

if ($aplicar) {
    echo "\nActualizados: " . $sync->aplicar($plan['cambios']) . " cursos\n";
} else {
    echo "\n(simulación: use --apply para escribir)\n";
}
