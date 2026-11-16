<?php
/**
 * Carga los cursos logísticos de "Cursos E-Learning LP.docx" (database/data/cursos_logisticos.json).
 *
 *   php scripts/cargar_cursos_logisticos.php           # simula y lista qué haría
 *   php scripts/cargar_cursos_logisticos.php --apply   # escribe
 *
 * Por cada curso del documento:
 *  - Si ya hay un curso LMS con ese nombre SIN producto: se crea su producto (y su ficha)
 *    y se liga. El curso LMS no se modifica.
 *  - Si no existe nada: se crea la ficha, el producto y un curso LMS en borrador.
 *  - Si ya tiene producto: se omite.
 *
 * Todo entra como borrador: producto inactivo y precio 0, categoría Logistics and Ports
 * Courses, sucursal Panamá y modalidad E-learning. El documento no trae precio, horas,
 * código ni imagen; se completan en el panel y entonces se activa.
 *
 * Es repetible: lo que ya existe se omite.
 */
define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';
\Core\Env::load(BASE_PATH . '/.env');
\Core\Config::load(BASE_PATH . '/config/config.php');

$aplicar = in_array('--apply', $argv, true);
$db = \Core\Database::getInstance();
$pdo = $db->getConnection();

const CATEGORIA_LOGISTICA = 10;   // categories: Logistics and Ports Courses
const LMS_CATEGORIA       = 4;    // lms_categories: LOGISTICS AND PORTS COURSES
const SUCURSAL_PANAMA     = 1;
const MODALIDAD_ELEARNING = 1;

/** Misma idea que Slug: minúsculas, sin acentos, guiones. */
function slug(string $t): string
{
    $t = mb_strtolower(trim($t), 'UTF-8');
    $t = strtr($t, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
    return trim(preg_replace('/[^a-z0-9]+/', '-', $t), '-');
}
function norm(string $t): string
{
    return preg_replace('/[^a-z0-9]+/', '', slug($t));
}

/** Parte "Inglés / Español", "Inglés (Español)" o un solo nombre. */
function nombres(string $titulo): array
{
    $t = trim(rtrim($titulo, " ."));
    $en = $t; $es = '';
    if (strpos($t, ' / ') !== false) {
        [$a, $b] = array_map('trim', explode(' / ', $t, 2));
        // "Curso de … / English name": el español viene primero
        if (preg_match('/^(Curso|Formaci[oó]n)\b/iu', $a)) { [$a, $b] = [$b, $a]; }
        [$en, $es] = [$a, $b];
    } elseif (preg_match('/^(.*?)\s*\((.+?)\)?$/u', $t, $m) && preg_match('/[áéíóúñ]|\b(de|del|la|el|y)\b/iu', $m[2])) {
        [$en, $es] = [trim($m[1]), trim($m[2])];
    } elseif (preg_match('/^(Curso|Formaci[oó]n)\b/iu', $t)) {
        [$en, $es] = ['', $t];
    }
    return [$en, $es];
}

/** Erratas evidentes del documento. */
$erratas = [
    'Working Alof'                 => 'Working Aloft',
    'Hor Work Training'            => 'Hot Work Training',
    'Excel Management Skills- Basic' => 'Excel Management Skills - Basic',
    'Maritime Labour Convention Course -MLC,' => 'Maritime Labour Convention Course - MLC',
    'Maritime Labour Convention Course -MLC'  => 'Maritime Labour Convention Course - MLC',
];

$cursos = json_decode(file_get_contents(BASE_PATH . '/database/data/cursos_logisticos.json'), true);

// Índices de lo existente
$prod = []; foreach ($db->fetchAll("SELECT id, name FROM products") as $p) $prod[norm($p['name'])] = $p;
$lms  = []; foreach ($db->fetchAll("SELECT id, title, product_id, slug FROM lms_courses") as $c) $lms[norm($c['title'])] = $c;
$fichas = []; foreach ($db->fetchAll("SELECT id, name FROM courses") as $f) $fichas[norm($f['name'])] = $f;
// Equivalencias de nombre conocidas entre el documento y lo ya cargado
$alias = [norm('Maritime Economy Workshop') => norm('Maritime Economy'),
          norm('Working Into Enclosed Spaces') => norm('Working Into Enclosed Space')];

$accion = ['omitido' => 0, 'ligar' => 0, 'nuevo' => 0];
$plan = [];
foreach ($cursos as $c) {
    [$en, $es] = nombres($c['titulo']);
    $nombre = $erratas[$en] ?? $en;
    if ($nombre === '') { $nombre = $es; }                 // curso sólo en español
    $k = norm($nombre); $k = $alias[$k] ?? $k;
    $objetivo = trim($c['objetivo']);

    if (isset($prod[$k])) { $accion['omitido']++; $plan[] = ['omitido', $nombre, "ya es producto #{$prod[$k]['id']}"]; continue; }
    $existente = $lms[$k] ?? null;
    if ($existente && $existente['product_id']) { $accion['omitido']++; $plan[] = ['omitido', $nombre, "curso LMS #{$existente['id']} ya ligado"]; continue; }

    // Al ligar a un curso LMS ya existente, producto y ficha llevan SU título,
    // para que tienda y portal no vuelvan a diferir (p. ej. «INCOTERMS» vs «Incoterms»).
    if ($existente) { $nombre = $existente['title']; }

    $accion[$existente ? 'ligar' : 'nuevo']++;
    $plan[] = [$existente ? 'ligar' : 'nuevo', $nombre, $existente ? "a curso LMS #{$existente['id']} ({$existente['title']})" : 'ficha + producto + curso LMS borrador',
               'k' => $k, 'nombre' => $nombre, 'es' => $es, 'objetivo' => $objetivo, 'lms' => $existente];
}

foreach ($plan as $i => $p) {
    printf("%2d %-8s %-72s %s\n", $i + 1, $p[0], mb_substr($p[1], 0, 72), $p[2]);
}
echo "\nResumen: " . json_encode($accion) . "\n";
if (!$aplicar) { echo "(simulación: use --apply para escribir)\n"; exit(0); }

$libreSlug = function (string $tabla, string $base) use ($db): string {
    $s = $base; $n = 1;
    while ($db->fetchOne("SELECT id FROM {$tabla} WHERE slug = ?", [$s])) { $s = $base . '-' . $n++; }
    return $s;
};

$pdo->beginTransaction();
try {
    $hechos = 0;
    foreach ($plan as $p) {
        if ($p[0] === 'omitido') continue;
        $nombre = $p['nombre']; $k = $p['k'];

        $ficha = $fichas[$k] ?? null;
        $fichaId = $ficha ? (int) $ficha['id'] : (int) $db->insert('courses', [
            'name' => $nombre, 'category_id' => CATEGORIA_LOGISTICA, 'description' => $p['objetivo'],
        ]);
        if (!$fichaId) { throw new RuntimeException("No se pudo crear la ficha de {$nombre}"); }

        $productoId = (int) $db->insert('products', [
            'name' => $nombre, 'slug' => $libreSlug('products', slug($nombre)),
            'category_id' => CATEGORIA_LOGISTICA, 'branch_id' => SUCURSAL_PANAMA, 'course_id' => $fichaId,
            'modality_id' => MODALIDAD_ELEARNING, 'description' => $p['objetivo'],
            'short_description' => $p['es'] !== '' && $p['es'] !== $nombre ? $p['es'] : null,
            'price' => 0, 'renewal_price' => 0, 'stock' => 50, 'status' => 'inactive',
        ]);
        if (!$productoId) { throw new RuntimeException("No se pudo crear el producto de {$nombre}"); }

        if ($p['lms']) {
            $db->update('lms_courses', ['product_id' => $productoId], 'id = :id', ['id' => (int) $p['lms']['id']]);
        } else {
            $db->insert('lms_courses', [
                'product_id' => $productoId, 'category_id' => LMS_CATEGORIA, 'title' => $nombre,
                'slug' => $libreSlug('lms_courses', slug($nombre)), 'description' => $p['objetivo'],
                'status' => 'draft', 'price' => 0, 'level' => 'beginner', 'pass_percentage' => 70,
            ]);
        }
        $hechos++;
    }
    $pdo->commit();
    echo "\nEscritos: $hechos cursos.\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "ERROR, no se escribió nada: " . $e->getMessage() . "\n");
    exit(1);
}
