<?php
/**
 * Exporta las preguntas cuyo quiz_id no existe en lms_quizzes, para que el
 * área académica pueda reasignarlas a un curso.
 *
 * Uso (desde la raíz del proyecto):
 *     php scripts/export_preguntas_huerfanas.php [carpeta_destino]
 *
 * Genera, en la carpeta indicada (por defecto ./export_huerfanas):
 *   1_sets_por_asignar.csv     un renglón por set, con muestra, para llenar
 *   2_catalogo_referencia.csv  cursos y quizzes vacíos disponibles
 *   3_preguntas_completas.csv  las preguntas con sus opciones
 *   4_bancos_grandes.csv       los sets de 100+ preguntas, aparte
 *
 * No modifica nada: es solo de lectura.
 */

require __DIR__ . '/../vendor/autoload.php';

use Core\Env;
use Core\Config;
use Core\Database;

Env::load(__DIR__ . '/../.env');
Config::load(__DIR__ . '/../config/config.php');

$outDir = $argv[1] ?? __DIR__ . '/../export_huerfanas';
if (!is_dir($outDir) && !mkdir($outDir, 0775, true) && !is_dir($outDir)) {
    fwrite(STDERR, "No se pudo crear la carpeta: $outDir\n");
    exit(1);
}
$outDir = realpath($outDir);

$db = Database::getInstance();

/** Limpia el HTML crudo que trae la importación. */
function limpiar($texto): string
{
    $t = (string) $texto;
    $t = str_replace(['\\n', '\\r', '\\t'], ' ', $t);        // saltos literales
    $t = html_entity_decode($t, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $t = strip_tags($t);
    $t = str_replace("\xC2\xA0", ' ', $t);                   // nbsp real
    $t = preg_replace('/\s+/u', ' ', $t);
    return trim($t);
}

/** Escribe un CSV con BOM UTF-8 para que Excel respete los acentos. */
function escribirCsv(string $ruta, array $cabecera, array $filas): void
{
    $fh = fopen($ruta, 'w');
    fwrite($fh, "\xEF\xBB\xBF");
    fputcsv($fh, $cabecera);
    foreach ($filas as $fila) {
        fputcsv($fh, $fila);
    }
    fclose($fh);
    printf("  %-32s %5d filas\n", basename($ruta), count($filas));
}

echo "Leyendo la base...\n";

// ── Sets huérfanos y su tamaño ───────────────────────────────────────────
$sets = $db->fetchAll(
    "SELECT q.quiz_id AS set_id, COUNT(*) AS preguntas
     FROM lms_questions q
     LEFT JOIN lms_quizzes z ON z.id = q.quiz_id
     WHERE z.id IS NULL
     GROUP BY q.quiz_id
     ORDER BY COUNT(*) DESC, q.quiz_id"
);

// Texto de preguntas que YA existen en algún quiz vivo (para marcar duplicados)
$vivas = [];
foreach ($db->fetchAll(
    "SELECT DISTINCT v.question
     FROM lms_questions v JOIN lms_quizzes zv ON zv.id = v.quiz_id"
) as $r) {
    $vivas[md5(trim((string) $r['question']))] = true;
}

// Todas las preguntas huérfanas con sus opciones
$preguntas = $db->fetchAll(
    "SELECT q.id, q.quiz_id AS set_id, q.question, q.points, q.order_num
     FROM lms_questions q
     LEFT JOIN lms_quizzes z ON z.id = q.quiz_id
     WHERE z.id IS NULL
     ORDER BY q.quiz_id, q.order_num, q.id"
);

$opciones = [];
foreach ($db->fetchAll(
    "SELECT o.question_id, o.option_text, o.is_correct
     FROM lms_question_options o
     JOIN lms_questions q ON q.id = o.question_id
     LEFT JOIN lms_quizzes z ON z.id = q.quiz_id
     WHERE z.id IS NULL
     ORDER BY o.question_id, o.order_num, o.id"
) as $o) {
    $opciones[(int) $o['question_id']][] = $o;
}

// Preguntas agrupadas por set
$porSet = [];
foreach ($preguntas as $p) {
    $porSet[(int) $p['set_id']][] = $p;
}

echo "Escribiendo en $outDir\n";

// ── 1) Un renglón por set, con muestra y columnas para llenar ────────────
$filasSets  = [];
$filasBanco = [];

foreach ($sets as $s) {
    $setId = (int) $s['set_id'];
    $n     = (int) $s['preguntas'];
    $lista = $porSet[$setId] ?? [];

    $muestra = [];
    foreach (array_slice($lista, 0, 3) as $p) {
        $muestra[] = mb_substr(limpiar($p['question']), 0, 160);
    }
    $muestra = array_pad($muestra, 3, '');

    $duplicadas = 0;
    foreach ($lista as $p) {
        if (isset($vivas[md5(trim((string) $p['question']))])) {
            $duplicadas++;
        }
    }

    $fila = [
        $setId,
        $n,
        $duplicadas,
        $n - $duplicadas,
        $muestra[0],
        $muestra[1],
        $muestra[2],
        '',   // CURSO ASIGNADO  <- lo llena el área académica
        '',   // QUIZ DESTINO ID
        '',   // NOTAS
    ];

    if ($n >= 100) {
        $filasBanco[] = $fila;
    } else {
        $filasSets[] = $fila;
    }
}

$cabeceraSets = [
    'SET_ID', 'PREGUNTAS', 'YA_DUPLICADAS', 'UNICAS',
    'MUESTRA_1', 'MUESTRA_2', 'MUESTRA_3',
    'CURSO_ASIGNADO (llenar)', 'QUIZ_DESTINO_ID (llenar)', 'NOTAS',
];

escribirCsv("$outDir/1_sets_por_asignar.csv", $cabeceraSets, $filasSets);
escribirCsv("$outDir/4_bancos_grandes.csv",   $cabeceraSets, $filasBanco);

// ── 2) Catálogo de referencia: cursos y quizzes vacíos ───────────────────
$ref = [];
foreach ($db->fetchAll(
    "SELECT c.id, c.title, c.status,
            (SELECT COUNT(*) FROM lms_quizzes z WHERE z.course_id = c.id) AS quizzes
     FROM lms_courses c ORDER BY c.title"
) as $c) {
    $ref[] = ['CURSO', (int) $c['id'], limpiar($c['title']), $c['status'], (int) $c['quizzes'], ''];
}
foreach ($db->fetchAll(
    "SELECT z.id, z.title, c.title AS curso
     FROM lms_quizzes z
     JOIN lms_courses c ON c.id = z.course_id
     WHERE NOT EXISTS (SELECT 1 FROM lms_questions q WHERE q.quiz_id = z.id)
     ORDER BY c.title, z.id"
) as $z) {
    $ref[] = ['QUIZ VACIO', (int) $z['id'], limpiar($z['title']), '', 0, limpiar($z['curso'])];
}

escribirCsv(
    "$outDir/2_catalogo_referencia.csv",
    ['TIPO', 'ID', 'TITULO', 'ESTADO', 'QUIZZES', 'CURSO'],
    $ref
);

// ── 3) Todas las preguntas con sus opciones ──────────────────────────────
$filasPreg = [];
foreach ($preguntas as $p) {
    $qid  = (int) $p['id'];
    $opts = $opciones[$qid] ?? [];

    $textos   = [];
    $correcta = '';
    foreach ($opts as $o) {
        $txt = limpiar($o['option_text']);
        $textos[] = $txt;
        if ((int) $o['is_correct'] === 1) {
            $correcta = $txt;
        }
    }
    $textos = array_pad(array_slice($textos, 0, 5), 5, '');

    $filasPreg[] = array_merge(
        [
            (int) $p['set_id'],
            $qid,
            (int) $p['order_num'],
            limpiar($p['question']),
            isset($vivas[md5(trim((string) $p['question']))]) ? 'SI' : 'no',
            $correcta,
        ],
        $textos
    );
}

escribirCsv(
    "$outDir/3_preguntas_completas.csv",
    ['SET_ID', 'PREGUNTA_ID', 'ORDEN', 'PREGUNTA', 'DUPLICADA', 'RESPUESTA_CORRECTA',
     'OPCION_1', 'OPCION_2', 'OPCION_3', 'OPCION_4', 'OPCION_5'],
    $filasPreg
);

echo "\nResumen\n";
printf("  sets por asignar (<100 preguntas): %d\n", count($filasSets));
printf("  bancos grandes (>=100):            %d\n", count($filasBanco));
printf("  preguntas exportadas:              %d\n", count($filasPreg));
echo "\nListo. Carpeta: $outDir\n";
