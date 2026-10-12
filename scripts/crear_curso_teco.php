<?php
/**
 * Completa el curso TECO — Working Into Enclosed Space (Entrenamiento de
 * Seguridad en Espacios Confinados) a partir del material de la carpeta TECO/.
 *
 * Uso (desde la raíz del proyecto):
 *     php scripts/crear_curso_teco.php              simula, no escribe
 *     php scripts/crear_curso_teco.php --aplicar    escribe en la base
 *
 * Fuentes:
 *   - Entrenamiento de Seguridad en Espacios-Confinados-Rev03.pptx  (lecciones)
 *   - EXT-Espacios Confinados.docx + CLAVE                          (examen)
 *
 * El curso ya existe en borrador y enlazado al producto 87 (código TECO);
 * este script rellena su contenido. Es idempotente: vuelve a dejar el mismo
 * temario y el mismo examen si se ejecuta otra vez.
 */

require __DIR__ . '/../vendor/autoload.php';

use Core\Env;
use Core\Config;
use Core\Database;

Env::load(__DIR__ . '/../.env');
Config::load(__DIR__ . '/../config/config.php');

const COURSE_ID    = 1547;   // Working Into Enclosed Space
const PRODUCT_ID   = 87;     // producto con código TECO
const LMS_CATEGORY = 4;      // LOGISTICS AND PORTS COURSES

$aplicar = in_array('--aplicar', $argv, true);
$db      = Database::getInstance();


// ── Examen teórico: preguntas extraídas de EXT + su clave ───────────────
$jsonPreguntas = __DIR__ . '/../TECO/preguntas_ext.json';
if (!is_file($jsonPreguntas)) {
    fwrite(STDERR, "Falta el archivo de preguntas: $jsonPreguntas\n");
    fwrite(STDERR, "Genéralo primero con el extractor del examen EXT.\n");
    exit(1);
}
$preguntas = json_decode(file_get_contents($jsonPreguntas), true);
if (!is_array($preguntas) || count($preguntas) !== 20) {
    fwrite(STDERR, "El archivo de preguntas no tiene las 20 preguntas esperadas.\n");
    exit(1);
}

// ── Resumen ─────────────────────────────────────────────────────────────
echo "Curso        : #" . COURSE_ID . " Working Into Enclosed Space (producto " . PRODUCT_ID . ", código TECO)\n";
echo "Categoría LMS: " . LMS_CATEGORY . " (Logistics and Ports Courses)\n";
echo "Lecciones    : " . count($lecciones) . "\n";
echo "Examen       : 1 quiz con " . count($preguntas) . " preguntas\n";

$curso = $db->fetchOne("SELECT id, title, status FROM lms_courses WHERE id = ?", [COURSE_ID]);
if (!$curso) {
    fwrite(STDERR, "\nNo existe el curso " . COURSE_ID . ".\n");
    exit(1);
}

if (!$aplicar) {
    echo "\nSimulación: no se ha escrito nada. Añade --aplicar para guardar.\n";
    exit(0);
}

// ── Escritura ───────────────────────────────────────────────────────────
$pdo = $db->getConnection();
$pdo->beginTransaction();

try {
    // Ficha del curso
    $pdo->prepare(
        "UPDATE lms_courses
         SET category_id = ?, course_code = 'TECO', learning_objectives = ?,
             level = 'beginner', pass_percentage = 70, completion_criteria = 'quiz'
         WHERE id = ?"
    )->execute([LMS_CATEGORY, $objetivos, COURSE_ID]);

    // Lecciones: se reemplazan para que el script sea repetible
    $pdo->prepare("DELETE FROM lms_lessons WHERE course_id = ?")->execute([COURSE_ID]);

    $insLeccion = $pdo->prepare(
        "INSERT INTO lms_lessons (course_id, title, content, type, order_num, duration, is_active)
         VALUES (?, ?, ?, 'text', ?, ?, 1)"
    );
    $orden = 0;
    foreach ($lecciones as $l) {
        $insLeccion->execute([COURSE_ID, $l['title'], $l['content'], ++$orden, $l['duration']]);
    }

    // Examen: se borra el anterior con todo su contenido
    $quizzesPrevios = $db->fetchAll("SELECT id FROM lms_quizzes WHERE course_id = ?", [COURSE_ID]);
    foreach ($quizzesPrevios as $q) {
        $qid = (int) $q['id'];
        $pdo->prepare("DELETE FROM lms_quiz_answers WHERE question_id IN (SELECT id FROM lms_questions WHERE quiz_id = ?)")->execute([$qid]);
        $pdo->prepare("DELETE FROM lms_question_options WHERE question_id IN (SELECT id FROM lms_questions WHERE quiz_id = ?)")->execute([$qid]);
        $pdo->prepare("DELETE FROM lms_questions WHERE quiz_id = ?")->execute([$qid]);
        $pdo->prepare("DELETE FROM lms_quizzes WHERE id = ?")->execute([$qid]);
    }

    $pdo->prepare(
        "INSERT INTO lms_quizzes (course_id, title, description, pass_percentage, time_limit, is_active)
         VALUES (?, ?, ?, 70, 40, 1)"
    )->execute([
        COURSE_ID,
        'Examen Teórico — Espacios Confinados',
        'Selección múltiple. Para aprobar se requiere un mínimo de 70 %. '
        . 'La evaluación práctica (uso de SCBA y técnica de evaluación de riesgo) se realiza por separado en el simulador.',
    ]);
    $quizId = (int) $pdo->lastInsertId();

    $insPregunta = $pdo->prepare(
        "INSERT INTO lms_questions (quiz_id, question, type, points, order_num)
         VALUES (?, ?, 'multiple_choice', 5, ?)"
    );
    $insOpcion = $pdo->prepare(
        "INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($preguntas as $p) {
        $insPregunta->execute([$quizId, $p['question'], (int) $p['num']]);
        $qid = (int) $pdo->lastInsertId();
        $o = 0;
        foreach ($p['options'] as $op) {
            $insOpcion->execute([$qid, $op['text'], (int) $op['is_correct'], ++$o]);
        }
    }

    $pdo->commit();

    echo "\nListo.\n";
    printf("  lecciones creadas : %d\n", count($lecciones));
    printf("  examen            : #%d con %d preguntas\n", $quizId, count($preguntas));
    echo "  el curso sigue en BORRADOR: revísalo y publícalo desde el panel.\n";
} catch (\Throwable $e) {
    $pdo->rollBack();
    fwrite(STDERR, "\nError, no se guardó nada: " . $e->getMessage() . "\n");
    exit(1);
}
