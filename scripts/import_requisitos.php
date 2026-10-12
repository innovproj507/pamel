<?php
/**
 * Importa la carpeta Requisitos/ a la tabla requirement_imports.
 *
 * Uso (desde la raíz del proyecto):
 *     php scripts/import_requisitos.php              simula, no escribe
 *     php scripts/import_requisitos.php --aplicar    escribe en la base
 *
 * Lee los documentos de R-C (curso regular), R-RF (refresco) y R-AS (ascenso),
 * extrae el título y la lista de requisitos, e intenta casar cada uno con un
 * producto del catálogo. Los que casan quedan en estado 'mapped'; el resto
 * queda 'pending' para resolverlos desde el panel.
 *
 * R-PDE se omite a propósito: son plantillas del formulario de solicitud,
 * no listas de requisitos.
 */

require __DIR__ . '/../vendor/autoload.php';

use Core\Env;
use Core\Config;
use Core\Database;

Env::load(__DIR__ . '/../.env');
Config::load(__DIR__ . '/../config/config.php');

const W_NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';

/** Carpetas con listas de requisitos y el modo al que corresponden. */
const CARPETAS = [
    'R-C'  => 'new',
    'R-RF' => 'refresher',
    'R-AS' => 'new',
];

$aplicar = in_array('--aplicar', $argv, true);
$raiz    = dirname(__DIR__);
$db      = Database::getInstance();

/**
 * Extrae título y requisitos de un documento.
 * @return array{titulo:string,requisitos:array<string>}|null
 */
function leerDocumento(string $ruta): ?array
{
    $zip = new ZipArchive();
    if ($zip->open($ruta) !== true) {
        return null;
    }
    $xml = $zip->getFromName('word/document.xml');
    $zip->close();
    if (!$xml) {
        return null;
    }

    $dom = new DOMDocument();
    if (!@$dom->loadXML($xml, LIBXML_NONET)) {
        return null;
    }

    $limpiar = static fn($s) => trim(preg_replace('/\s+/u', ' ', (string) $s));

    foreach ($dom->getElementsByTagNameNS(W_NS, 'tbl') as $tabla) {
        $filas = $tabla->getElementsByTagNameNS(W_NS, 'tr');
        if ($filas->length < 2) {
            continue;
        }
        // La tabla de requisitos se reconoce por su cabecera
        if (stripos($limpiar($filas->item(0)->textContent), 'DESCRIPTION') === false) {
            continue;
        }

        $celdas = $filas->item(1)->getElementsByTagNameNS(W_NS, 'tc');
        if ($celdas->length < 2) {
            continue;
        }

        $parrafos = [];
        foreach ($celdas->item(1)->getElementsByTagNameNS(W_NS, 'p') as $p) {
            $t = $limpiar($p->textContent);
            if ($t !== '') {
                $parrafos[] = $t;
            }
        }
        if (count($parrafos) < 2) {
            continue;
        }

        return [
            'titulo'     => array_shift($parrafos),
            'requisitos' => $parrafos,
        ];
    }

    return null;
}

/** Normaliza un título para poder compararlo. */
function normalizar(string $s): string
{
    $s = mb_strtolower($s, 'UTF-8');
    $s = strtr($s, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','´'=>'','’'=>'',"'"=>'']);
    $s = preg_replace('/\([^)]*\)/u', ' ', $s);        // quita "(RULE VI/2)"
    $s = preg_replace('/[^a-z0-9 ]/u', ' ', $s);
    return trim(preg_replace('/\s+/', ' ', $s));
}

/**
 * ¿Este requisito debería pedir un archivo al solicitante?
 * Los informativos (referencias al programa del curso) no lo piden.
 */
function pideArchivo(string $label): bool
{
    $l = mb_strtolower($label, 'UTF-8');

    $informativos = [
        'as per structure of the course program',
        'this course is intended',
        'the participant must ensure',
    ];
    foreach ($informativos as $frase) {
        if (str_contains($l, $frase)) {
            return false;
        }
    }
    return true;
}

// ── Índice de productos para el cruce ───────────────────────────────────
$productos = $db->fetchAll("SELECT id, name FROM products WHERE status = 'active' ORDER BY name");
$indice    = [];
foreach ($productos as $p) {
    $indice[normalizar($p['name'])] = (int) $p['id'];
}

/** Busca el producto que corresponde a un título, exacto o por contención. */
function buscarProducto(string $titulo, array $indice): ?int
{
    $n = normalizar($titulo);
    if ($n === '') {
        return null;
    }
    if (isset($indice[$n])) {
        return $indice[$n];
    }
    foreach ($indice as $clave => $id) {
        if ($clave !== '' && (str_contains($clave, $n) || str_contains($n, $clave))) {
            return $id;
        }
    }
    return null;
}

// ── Recorrido de las carpetas ───────────────────────────────────────────
$filas = [];
$stats = ['leidos' => 0, 'sin_tabla' => 0, 'casados' => 0, 'pendientes' => 0];

foreach (CARPETAS as $carpeta => $modo) {
    $dir = "$raiz/Requisitos/$carpeta";
    if (!is_dir($dir)) {
        fwrite(STDERR, "  ! no existe la carpeta $carpeta\n");
        continue;
    }

    foreach (glob("$dir/*.docx") as $ruta) {
        if (basename($ruta)[0] === '~') {
            continue;   // archivos temporales de Word
        }

        $doc = leerDocumento($ruta);
        if (!$doc) {
            $stats['sin_tabla']++;
            continue;
        }
        $stats['leidos']++;

        $productId = buscarProducto($doc['titulo'], $indice);
        $productId ? $stats['casados']++ : $stats['pendientes']++;

        $filas[] = [
            'folder'       => $carpeta,
            'mode'         => $modo,
            'file_name'    => basename($ruta),
            'doc_title'    => mb_substr($doc['titulo'], 0, 500),
            'requirements' => json_encode(array_map(
                static fn($r) => ['label' => $r, 'requires_file' => pideArchivo($r) ? 1 : 0],
                $doc['requisitos']
            ), JSON_UNESCAPED_UNICODE),
            'product_id'   => $productId,
            'status'       => $productId ? 'mapped' : 'pending',
        ];
    }
}

// ── Colisiones: dos documentos del mismo tipo apuntando a un mismo curso ─
$porDestino = [];
foreach ($filas as $i => $f) {
    if ($f['product_id'] !== null) {
        $porDestino[$f['product_id'] . '|' . $f['folder']][] = $i;
    }
}

$colisiones = 0;
foreach ($porDestino as $clave => $indices) {
    if (count($indices) < 2) {
        continue;
    }
    // Ninguno de los dos es de fiar: que lo decida una persona
    [$pid, $carpeta] = explode('|', $clave);
    foreach ($indices as $i) {
        $filas[$i]['product_id'] = null;
        $filas[$i]['status']     = 'pending';
        $stats['casados']--;
        $stats['pendientes']++;
        $colisiones++;
    }
    fwrite(STDERR, sprintf(
        "  ! %s: %d documentos apuntaban al curso %s; quedan pendientes\n",
        $carpeta, count($indices), $pid
    ));
}

// ── Resultado ───────────────────────────────────────────────────────────
printf("Documentos con requisitos : %d\n", $stats['leidos']);
printf("Sin tabla reconocible     : %d\n", $stats['sin_tabla']);
printf("Casan con un curso        : %d\n", $stats['casados']);
printf("Quedan por mapear         : %d\n", $stats['pendientes']);
if ($colisiones) {
    printf("  de ellos, por colisión de títulos: %d\n", $colisiones);
}

if (!$aplicar) {
    echo "\nSimulación: no se ha escrito nada. Añade --aplicar para guardar.\n";
    exit(0);
}

$pdo = $db->getConnection();
$pdo->beginTransaction();

try {
    $sql = "INSERT INTO requirement_imports
                (folder, file_name, doc_title, requirements, product_id, status)
            VALUES (:folder, :file_name, :doc_title, :requirements, :product_id, :status)
            ON DUPLICATE KEY UPDATE
                doc_title    = VALUES(doc_title),
                requirements = VALUES(requirements),
                product_id   = COALESCE(requirement_imports.product_id, VALUES(product_id)),
                status       = IF(requirement_imports.status = 'pending', VALUES(status), requirement_imports.status)";

    $stmt = $pdo->prepare($sql);
    foreach ($filas as $f) {
        $stmt->execute([
            ':folder'       => $f['folder'],
            ':file_name'    => $f['file_name'],
            ':doc_title'    => $f['doc_title'],
            ':requirements' => $f['requirements'],
            ':product_id'   => $f['product_id'],
            ':status'       => $f['status'],
        ]);
    }

    $pdo->commit();
    printf("\nGuardados %d documentos en requirement_imports.\n", count($filas));
    echo "Siguiente paso: /manager/requisitos para revisarlos y publicarlos.\n";
} catch (\Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "Error al guardar: " . $e->getMessage() . "\n");
    exit(1);
}
