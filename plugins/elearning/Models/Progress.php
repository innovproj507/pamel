<?php

namespace Plugins\Elearning\Models;

use Core\Model;

/**
 * Progreso real de los estudiantes.
 *
 * No usa lms_enrollments.progress_percentage: esa columna existe pero
 * nadie la escribe —las 216 matrículas están a 0.00 con 751 lecciones
 * completadas y 211 exámenes aprobados—, así que el panel enseñaba cero
 * para todo el mundo. Todo se calcula aquí desde lms_lesson_progress y
 * lms_quiz_attempts, que es donde está el dato de verdad.
 */
class Progress extends Model
{
    protected $table = 'lms_enrollments';

    /**
     * Matrículas con su progreso calculado.
     *
     * @param array $filtros 'course_id', 'student_id', 'estado'
     *   ('sin_empezar'|'en_curso'|'completado'), 'buscar'
     */
    public function matriculas(array $filtros = [], int $limite = 500): array
    {
        $filas = $this->filasCrudas($filtros, $limite);

        foreach ($filas as &$f) {
            $this->calcular($f);
        }
        unset($f);

        // El estado se filtra después porque es calculado
        if (!empty($filtros['estado'])) {
            $filas = array_values(array_filter($filas, fn($f) => $f['estado'] === $filtros['estado']));
        }

        return $filas;
    }

    /**
     * Las filas con sus recuentos, sin calcular estado ni etapa.
     *
     * Está aparte porque el resumen necesita recorrer TODAS las matrículas
     * y no le hace falta la etapa, que cuesta una consulta por fila.
     *
     * @param int|null $limite null = sin tope.
     */
    private function filasCrudas(array $filtros, ?int $limite): array
    {
        $where  = ['u.id IS NOT NULL'];
        $params = [];

        if (!empty($filtros['course_id'])) {
            $where[]  = 'e.course_id = ?';
            $params[] = (int) $filtros['course_id'];
        }
        if (!empty($filtros['student_id'])) {
            $where[]  = 'e.student_id = ?';
            $params[] = (int) $filtros['student_id'];
        }
        if (!empty($filtros['buscar'])) {
            $where[]  = '(u.name LIKE ? OR u.email LIKE ?)';
            $params[] = '%' . $filtros['buscar'] . '%';
            $params[] = '%' . $filtros['buscar'] . '%';
        }

        $sql = "SELECT
                    e.id              AS matricula_id,
                    e.student_id,
                    e.course_id,
                    e.enrolled_at,
                    e.last_accessed_at,
                    u.name            AS estudiante,
                    u.email,
                    c.title           AS curso,
                    (SELECT COUNT(*) FROM lms_lessons l
                      WHERE l.course_id = c.id AND l.is_active = 1)            AS lecciones,
                    (SELECT COUNT(DISTINCT p.lesson_id)
                       FROM lms_lesson_progress p
                       JOIN lms_lessons l2 ON l2.id = p.lesson_id
                      WHERE p.student_id = e.student_id
                        AND l2.course_id = c.id AND l2.is_active = 1)          AS lecciones_hechas,
                    (SELECT COUNT(*) FROM lms_quizzes q
                      WHERE q.course_id = c.id AND q.is_active = 1)            AS examenes,
                    (SELECT COUNT(DISTINCT a.quiz_id)
                       FROM lms_quiz_attempts a
                       JOIN lms_quizzes q2 ON q2.id = a.quiz_id
                      WHERE a.student_id = e.student_id
                        AND q2.course_id = c.id AND a.passed = 1)              AS examenes_aprobados,
                    (SELECT COUNT(*)
                       FROM lms_quiz_attempts a2
                       JOIN lms_quizzes q3 ON q3.id = a2.quiz_id
                      WHERE a2.student_id = e.student_id AND q3.course_id = c.id) AS intentos,
                    (SELECT MAX(a3.completed_at)
                       FROM lms_quiz_attempts a3
                       JOIN lms_quizzes q4 ON q4.id = a3.quiz_id
                      WHERE a3.student_id = e.student_id AND q4.course_id = c.id) AS ultimo_examen
                FROM lms_enrollments e
                JOIN users u       ON u.id = e.student_id
                JOIN lms_courses c ON c.id = e.course_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY u.name ASC, c.title ASC"
                . ($limite !== null ? ' LIMIT ' . max(1, $limite) : '');

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Añade a una fila su progreso, etapa y estado.
     *
     * Completado = todas las lecciones activas hechas y todos los exámenes
     * activos aprobados. Un curso sin lecciones ni exámenes no se puede
     * completar, así que se queda en "sin empezar".
     */
    private function calcular(array &$f): void
    {
        $this->evaluar($f);

        // En qué va: la primera lección activa sin completar, o el examen
        $f['etapa'] = $this->etapa((int) $f['student_id'], (int) $f['course_id'], $f);
    }

    /**
     * Progreso, estado y pendientes de una fila.
     *
     * Aparte de calcular() para que el resumen aplique exactamente la
     * misma regla que el listado sin pagar una consulta por fila.
     */
    private function evaluar(array &$f): void
    {
        $lec    = (int) $f['lecciones'];
        $hechas = (int) $f['lecciones_hechas'];
        $ex     = (int) $f['examenes'];
        $apro   = (int) $f['examenes_aprobados'];

        $partes = $lec + $ex;
        $logro  = min($hechas, $lec) + min($apro, $ex);

        $f['progreso']  = $partes > 0 ? (int) round(($logro / $partes) * 100) : 0;
        $f['pendiente_lecciones'] = max(0, $lec - $hechas);
        $f['pendiente_examenes']  = max(0, $ex - $apro);

        if ($partes === 0 || $logro === 0) {
            $f['estado'] = 'sin_empezar';
        } elseif ($logro >= $partes) {
            $f['estado'] = 'completado';
        } else {
            $f['estado'] = 'en_curso';
        }
    }

    /** Descripción corta de en qué punto del curso está. */
    private function etapa(int $studentId, int $courseId, array $f): string
    {
        if ($f['estado'] === 'completado') {
            return 'Curso completado';
        }
        if ((int) $f['pendiente_lecciones'] > 0) {
            $l = $this->db->fetchOne(
                "SELECT l.title FROM lms_lessons l
                  WHERE l.course_id = ? AND l.is_active = 1
                    AND l.id NOT IN (SELECT lesson_id FROM lms_lesson_progress WHERE student_id = ?)
                  ORDER BY l.order_num ASC, l.id ASC LIMIT 1",
                [$courseId, $studentId]
            );
            return $l ? 'Lección: ' . $l['title'] : 'Lecciones pendientes';
        }
        if ((int) $f['pendiente_examenes'] > 0) {
            return (int) $f['intentos'] > 0 ? 'Examen no aprobado' : 'Pendiente de examen';
        }
        return 'Sin empezar';
    }

    /**
     * Totales para los indicadores del panel.
     *
     * Recorre todas las matrículas, sin el tope de filas del listado: con
     * el tope, al pasar de 500 las tarjetas habrían empezado a quedarse
     * cortas sin avisar de nada.
     */
    public function resumen(array $filtros = []): array
    {
        $filas = $this->filasCrudas($filtros, null);
        foreach ($filas as &$f) {
            $this->evaluar($f);
        }
        unset($f);

        if (!empty($filtros['estado'])) {
            $filas = array_values(array_filter($filas, fn($f) => $f['estado'] === $filtros['estado']));
        }

        $r = [
            'matriculas'   => count($filas),
            'estudiantes'  => count(array_unique(array_column($filas, 'student_id'))),
            'sin_empezar'  => 0,
            'en_curso'     => 0,
            'completados'  => 0,
            'con_pendiente_examen'   => 0,
            'con_pendiente_leccion'  => 0,
            'progreso_medio'         => 0,
        ];

        $suma = 0;
        foreach ($filas as $f) {
            $r[$f['estado'] === 'completado' ? 'completados'
                : ($f['estado'] === 'en_curso' ? 'en_curso' : 'sin_empezar')]++;
            if ((int) $f['pendiente_examenes'] > 0)  { $r['con_pendiente_examen']++; }
            if ((int) $f['pendiente_lecciones'] > 0) { $r['con_pendiente_leccion']++; }
            $suma += (int) $f['progreso'];
        }
        $r['progreso_medio'] = $filas ? (int) round($suma / count($filas)) : 0;

        return $r;
    }

    /**
     * Lo mismo que resumen(), pero una fila por curso.
     *
     * Se agrupa en PHP sobre las mismas filas que alimentan el listado,
     * en vez de con un GROUP BY aparte, para que un curso y el detalle de
     * sus estudiantes no puedan contar cosas distintas.
     */
    public function porCurso(array $filtros = []): array
    {
        $filas = $this->filasCrudas($filtros, null);
        foreach ($filas as &$f) {
            $this->evaluar($f);
        }
        unset($f);

        $cursos = [];
        foreach ($filas as $f) {
            $id = (int) $f['course_id'];

            if (!isset($cursos[$id])) {
                $cursos[$id] = [
                    'course_id'   => $id,
                    'curso'       => $f['curso'],
                    'lecciones'   => (int) $f['lecciones'],
                    'examenes'    => (int) $f['examenes'],
                    'matriculas'  => 0,
                    'estudiantes' => [],
                    'completados' => 0,
                    'en_curso'    => 0,
                    'sin_empezar' => 0,
                    'con_pendiente_examen' => 0,
                    'suma'        => 0,
                ];
            }

            $c = &$cursos[$id];
            $c['matriculas']++;
            $c['estudiantes'][(int) $f['student_id']] = true;
            $c[$f['estado'] === 'completado' ? 'completados'
                : ($f['estado'] === 'en_curso' ? 'en_curso' : 'sin_empezar')]++;
            if ((int) $f['pendiente_examenes'] > 0) {
                $c['con_pendiente_examen']++;
            }
            $c['suma'] += (int) $f['progreso'];
            unset($c);
        }

        foreach ($cursos as &$c) {
            $c['estudiantes']     = count($c['estudiantes']);
            $c['progreso_medio']  = $c['matriculas'] > 0 ? (int) round($c['suma'] / $c['matriculas']) : 0;
            unset($c['suma']);
        }
        unset($c);

        // Primero los que tienen gente sin terminar: es donde hay que mirar.
        usort($cursos, function (array $a, array $b): int {
            $pendientesA = $a['matriculas'] - $a['completados'];
            $pendientesB = $b['matriculas'] - $b['completados'];
            return $pendientesB <=> $pendientesA ?: strcasecmp($a['curso'], $b['curso']);
        });

        return $cursos;
    }

    /** Exámenes realizados por un estudiante, con su calificación. */
    public function examenesDeEstudiante(int $studentId): array
    {
        return $this->db->fetchAll(
            "SELECT a.id, a.score, a.passed, a.started_at, a.completed_at,
                    q.title AS examen, q.pass_percentage,
                    c.title AS curso, c.id AS course_id,
                    (SELECT COUNT(*) FROM lms_quiz_answers WHERE attempt_id = a.id) AS respuestas
               FROM lms_quiz_attempts a
               JOIN lms_quizzes q  ON q.id = a.quiz_id
               JOIN lms_courses c  ON c.id = q.course_id
              WHERE a.student_id = ?
              ORDER BY a.id DESC",
            [$studentId]
        );
    }

    /** Un intento con todo lo necesario para el acta. */
    public function intentoCompleto(int $attemptId): ?array
    {
        $a = $this->db->fetchOne(
            "SELECT a.*, q.title AS quiz_title, q.pass_percentage,
                    c.title AS course_title,
                    u.name AS student_name, u.email AS student_email
               FROM lms_quiz_attempts a
               JOIN lms_quizzes q ON q.id = a.quiz_id
               JOIN lms_courses c ON c.id = q.course_id
               JOIN users u       ON u.id = a.student_id
              WHERE a.id = ?",
            [$attemptId]
        );
        if (!$a) {
            return null;
        }

        $a['answers'] = $this->db->fetchAll(
            "SELECT qa.is_correct, q.question,
                    qo.option_text AS selected_text,
                    (SELECT option_text FROM lms_question_options
                      WHERE question_id = q.id AND is_correct = 1 LIMIT 1) AS correct_text
               FROM lms_quiz_answers qa
               JOIN lms_questions q ON q.id = qa.question_id
               LEFT JOIN lms_question_options qo ON qo.id = qa.selected_option_id
              WHERE qa.attempt_id = ?
              ORDER BY qa.id ASC",
            [$attemptId]
        );

        return $a;
    }
}
