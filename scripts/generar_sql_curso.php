<?php
/**
 * Genera el SQL para publicar un curso en otro entorno (producción).
 *
 * Uso (desde la raíz del proyecto):
 *     php scripts/generar_sql_curso.php teco > database/schema/curso_teco.sql
 *
 * El SQL resultante no lleva IDs cableados: localiza el producto por su
 * course_code y crea o reutiliza la fila de lms_courses. Es repetible —
 * volver a ejecutarlo deja el mismo temario y el mismo examen.
 */

$cursos = [
    'teco' => __DIR__ . '/../TECO/definicion_curso.php',
];

$clave = strtolower($argv[1] ?? '');
if (!isset($cursos[$clave])) {
    fwrite(STDERR, "Uso: php scripts/generar_sql_curso.php <" . implode('|', array_keys($cursos)) . ">\n");
    exit(1);
}

$def = require $cursos[$clave];

$jsonPreguntas = dirname($cursos[$clave]) . '/preguntas_ext.json';
$preguntas = is_file($jsonPreguntas)
    ? json_decode(file_get_contents($jsonPreguntas), true)
    : [];

/** Escapa un literal para MySQL. */
function q(?string $v): string
{
    if ($v === null) {
        return 'NULL';
    }
    return "'" . str_replace(
        ['\\', "'", "\r", "\n", "\x1a"],
        ['\\\\', "''", '\\r', '\\n', '\\Z'],
        $v
    ) . "'";
}

$code  = $def['course_code'];
$fecha = date('Y-m-d');
$out   = [];

$out[] = "-- ─────────────────────────────────────────────────────────────────────";
$out[] = "-- Curso {$code} — {$def['title']}";
$out[] = "-- Generado el {$fecha} desde el material de la carpeta {$code}/";
$out[] = "--";
$out[] = "-- No lleva IDs fijos: busca el producto por course_code y crea o reutiliza";
$out[] = "-- la fila de lms_courses. Se puede ejecutar más de una vez sin duplicar.";
$out[] = "--";
$out[] = "-- Ejecutar con:  mysql -u USUARIO -p BASE < este_archivo.sql";
$out[] = "-- ─────────────────────────────────────────────────────────────────────";
$out[] = '';
$out[] = '-- El cotejo de la conexión debe coincidir con el de las tablas:';
$out[] = '-- sin esto, comparar una variable con una columna da el error #1267.';
$out[] = 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;';
$out[] = 'SET @code := ' . q($code) . ';';
$out[] = '';
$out[] = 'START TRANSACTION;';
$out[] = '';
$out[] = '-- ── 1. Producto de referencia ────────────────────────────────────────';
$out[] = 'SET @product_id := (SELECT id FROM products';
$out[] = '    WHERE course_code COLLATE utf8mb4_unicode_ci = @code COLLATE utf8mb4_unicode_ci';
$out[] = '    LIMIT 1);';
$out[] = '';
$out[] = '-- Diagnóstico. Si sale NO ENCONTRADO el script no escribe nada:';
$out[] = '-- todas las inserciones van condicionadas a que exista el curso.';
$out[] = "SELECT IF(@product_id IS NULL,";
$out[] = "    CONCAT('NO ENCONTRADO: ningún producto con course_code = ', @code),";
$out[] = "    CONCAT('OK: producto #', @product_id)) AS comprobacion_producto;";
$out[] = '';
$out[] = '-- ── 2. Categoría LMS equivalente a la del producto ───────────────────';
$out[] = 'SET @category_id := (';
$out[] = '    SELECT lc.id FROM lms_categories lc';
$out[] = '    JOIN categories c ON UPPER(c.name) COLLATE utf8mb4_unicode_ci';
$out[] = '                      = UPPER(lc.name) COLLATE utf8mb4_unicode_ci';
$out[] = '    JOIN products p   ON p.category_id = c.id';
$out[] = '    WHERE p.id = @product_id LIMIT 1';
$out[] = ');';
$out[] = '';
$out[] = '-- ── 3. Curso: se reutiliza si ya existe ──────────────────────────────';
$out[] = 'SET @course_id := (SELECT id FROM lms_courses WHERE product_id = @product_id LIMIT 1);';
$out[] = '';
$out[] = 'INSERT INTO lms_courses';
$out[] = '    (product_id, course_code, category_id, title, slug, description,';
$out[] = '     learning_objectives, level, status, pass_percentage, is_free, completion_criteria)';
$out[] = 'SELECT @product_id, @code, @category_id, p.name,';
$out[] = "       CONCAT(p.slug, '-', LOWER(@code)), p.description,";
$out[] = '       ' . q($def['objetivos']) . ',';
$out[] = '       ' . q($def['level']) . ", 'draft', " . (int) $def['pass_percentage'] . ', 0, ' . q($def['completion_criteria']);
$out[] = 'FROM products p';
$out[] = 'WHERE p.id = @product_id AND @course_id IS NULL;';
$out[] = '';
$out[] = 'SET @course_id := COALESCE(@course_id, LAST_INSERT_ID());';
$out[] = '';
$out[] = '-- Si ya existía, se actualiza la ficha';
$out[] = 'UPDATE lms_courses SET';
$out[] = '    course_code         = @code,';
$out[] = '    category_id         = COALESCE(@category_id, category_id),';
$out[] = '    learning_objectives = ' . q($def['objetivos']) . ',';
$out[] = '    level               = ' . q($def['level']) . ',';
$out[] = '    pass_percentage     = ' . (int) $def['pass_percentage'] . ',';
$out[] = '    completion_criteria = ' . q($def['completion_criteria']);
$out[] = 'WHERE id = @course_id;';
$out[] = '';

// ── Lecciones ───────────────────────────────────────────────────────────
$out[] = '-- ── 4. Lecciones ─────────────────────────────────────────────────────';
$out[] = '-- Se reemplazan por completo para que el script sea repetible.';
$out[] = 'DELETE FROM lms_lessons WHERE course_id = @course_id;';
$out[] = '';
foreach ($def['lecciones'] as $i => $l) {
    $out[] = 'INSERT INTO lms_lessons (course_id, title, content, type, order_num, duration, is_active)';
    $out[] = sprintf(
        "SELECT @course_id, %s, %s, 'text', %d, %d, 1 FROM dual WHERE @course_id IS NOT NULL;",
        q($l['title']),
        q($l['content']),
        $i + 1,
        (int) $l['duration']
    );
    $out[] = '';
}

// ── Examen ──────────────────────────────────────────────────────────────
if ($preguntas) {
    $quiz = $def['quiz'];

    $out[] = '-- ── 5. Examen teórico ────────────────────────────────────────────────';
    $out[] = '-- Se borra el examen anterior del curso con todo su contenido.';
    $out[] = 'DELETE qa FROM lms_quiz_answers qa';
    $out[] = '    JOIN lms_questions q ON q.id = qa.question_id';
    $out[] = '    JOIN lms_quizzes z   ON z.id = q.quiz_id';
    $out[] = '    WHERE z.course_id = @course_id;';
    $out[] = 'DELETE o FROM lms_question_options o';
    $out[] = '    JOIN lms_questions q ON q.id = o.question_id';
    $out[] = '    JOIN lms_quizzes z   ON z.id = q.quiz_id';
    $out[] = '    WHERE z.course_id = @course_id;';
    $out[] = 'DELETE q FROM lms_questions q';
    $out[] = '    JOIN lms_quizzes z ON z.id = q.quiz_id';
    $out[] = '    WHERE z.course_id = @course_id;';
    $out[] = 'DELETE FROM lms_quizzes WHERE course_id = @course_id;';
    $out[] = '';
    $out[] = 'SET @quiz_id := NULL;';
    $out[] = 'INSERT INTO lms_quizzes (course_id, title, description, pass_percentage, time_limit, is_active)';
    $out[] = 'SELECT @course_id, ' . q($quiz['title']) . ', ' . q($quiz['description'])
           . ', ' . (int) $quiz['pass_percentage'] . ', ' . (int) $quiz['time_limit'] . ', 1'
           . ' FROM dual WHERE @course_id IS NOT NULL;';
    $out[] = 'SET @quiz_id := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);';
    $out[] = '';

    foreach ($preguntas as $p) {
        $out[] = "-- Pregunta {$p['num']}";
        $out[] = 'INSERT INTO lms_questions (quiz_id, question, type, points, order_num)';
        $out[] = 'SELECT @quiz_id, ' . q($p['question']) . ", 'multiple_choice', "
               . (int) $quiz['points'] . ', ' . (int) $p['num']
               . ' FROM dual WHERE @quiz_id IS NOT NULL;';
        $out[] = 'SET @q := IF(ROW_COUNT() > 0, LAST_INSERT_ID(), NULL);';

        foreach ($p['options'] as $j => $op) {
            $out[] = 'INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num)';
            $out[] = sprintf(
                'SELECT @q, %s, %d, %d FROM dual WHERE @q IS NOT NULL;',
                q($op['text']),
                (int) $op['is_correct'],
                $j + 1
            );
        }
        $out[] = '';
    }
}

// ── Cierre ──────────────────────────────────────────────────────────────
$out[] = 'COMMIT;';
$out[] = '';
$out[] = '-- ── Comprobación ─────────────────────────────────────────────────────';
$out[] = "SELECT c.id AS curso, c.title, c.status, c.course_code,";
$out[] = '       (SELECT COUNT(*) FROM lms_lessons  WHERE course_id = c.id) AS lecciones,';
$out[] = '       (SELECT COUNT(*) FROM lms_quizzes  WHERE course_id = c.id) AS examenes,';
$out[] = '       (SELECT COUNT(*) FROM lms_questions q JOIN lms_quizzes z ON z.id = q.quiz_id';
$out[] = '         WHERE z.course_id = c.id) AS preguntas';
$out[] = 'FROM lms_courses c WHERE c.id = @course_id;';
$out[] = '';
$out[] = '-- El curso queda en BORRADOR. Revísalo y publícalo desde el panel.';

echo implode("\n", $out) . "\n";
