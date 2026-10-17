<?php

namespace Plugins\Elearning\Models;

use Core\Model;

class Quiz extends Model
{
    protected $table = 'lms_quizzes';

    /**
     * Get course-level quizzes (not attached to a specific lesson).
     */
    public function byCourse($courseId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE course_id = ? AND (lesson_id IS NULL OR lesson_id = 0) ORDER BY created_at ASC",
            [$courseId]
        );
    }

    /**
     * Get the quiz (with questions+options) attached to a specific lesson, or false.
     */
    public function findByLesson($lessonId)
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE lesson_id = ? LIMIT 1",
            [(int) $lessonId]
        );
    }

    /**
     * Get quiz with its questions and their options.
     */
    public function withQuestionsAndOptions($quizId)
    {
        $quiz = $this->find($quizId);
        if (!$quiz) return false;

        $questions = $this->db->fetchAll(
            "SELECT * FROM lms_questions WHERE quiz_id = ? ORDER BY order_num ASC, id ASC",
            [$quizId]
        );

        foreach ($questions as &$q) {
            $q['options'] = $this->db->fetchAll(
                "SELECT * FROM lms_question_options WHERE question_id = ? ORDER BY order_num ASC",
                [$q['id']]
            );
        }

        $quiz['questions'] = $questions;
        return $quiz;
    }

    /**
     * Crea una pregunta con sus opciones de forma atómica.
     *
     * @param array $question ['quiz_id','question','type','points','order_num']
     * @param array $options  [['text' => ..., 'is_correct' => 0|1], ...]
     * @return int|false  ID de la pregunta creada, o false si algo falló.
     */
    /**
     * Reescribe una pregunta y sus opciones.
     *
     * Las opciones se reemplazan por completo, pero las respuestas ya dadas
     * por los estudiantes apuntan a la opción elegida: al borrarlas se
     * quedarían huérfanas. Por eso se reutilizan los ids existentes cuando
     * hay los mismos o más, y sólo se borran los que sobren.
     *
     * @param array $opciones [['text' => string, 'is_correct' => 0|1], …]
     */
    public function updateQuestionWithOptions(int $questionId, array $datos, array $opciones): bool
    {
        if (!$opciones) {
            return false;
        }

        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $pdo->prepare(
                'UPDATE lms_questions SET question = ?, points = ?, order_num = ? WHERE id = ?'
            )->execute([
                $datos['question'],
                (int) ($datos['points'] ?? 1),
                (int) ($datos['order_num'] ?? 0),
                $questionId,
            ]);

            $existentes = $this->db->fetchAll(
                'SELECT id FROM lms_question_options WHERE question_id = ? ORDER BY order_num, id',
                [$questionId]
            );
            $ids = array_column($existentes, 'id');

            $actualizar = $pdo->prepare(
                'UPDATE lms_question_options SET option_text = ?, is_correct = ?, order_num = ? WHERE id = ?'
            );
            $insertar = $pdo->prepare(
                'INSERT INTO lms_question_options (question_id, option_text, is_correct, order_num) VALUES (?, ?, ?, ?)'
            );

            foreach (array_values($opciones) as $i => $o) {
                if (isset($ids[$i])) {
                    $actualizar->execute([$o['text'], (int) $o['is_correct'], $i + 1, $ids[$i]]);
                } else {
                    $insertar->execute([$questionId, $o['text'], (int) $o['is_correct'], $i + 1]);
                }
            }

            // Las que sobran: se borran junto a las respuestas que las citen
            $sobran = array_slice($ids, count($opciones));
            if ($sobran) {
                $marcas = implode(',', array_fill(0, count($sobran), '?'));
                $pdo->prepare("UPDATE lms_quiz_answers SET selected_option_id = NULL WHERE selected_option_id IN ({$marcas})")
                    ->execute($sobran);
                $pdo->prepare("DELETE FROM lms_question_options WHERE id IN ({$marcas})")->execute($sobran);
            }

            $pdo->commit();

            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log('Quiz::updateQuestionWithOptions: ' . $e->getMessage());

            return false;
        }
    }

    public function createQuestionWithOptions(array $question, array $options)
    {
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $questionId = $this->db->insert('lms_questions', [
                'quiz_id'   => (int) ($question['quiz_id'] ?? 0),
                'question'  => trim((string) ($question['question'] ?? '')),
                'type'      => $question['type'] ?? 'multiple_choice',
                'points'    => (int) ($question['points'] ?? 1),
                'order_num' => (int) ($question['order_num'] ?? 0),
            ]);

            if (!$questionId) {
                throw new \RuntimeException('No se pudo insertar la pregunta.');
            }

            foreach (array_values($options) as $i => $option) {
                $inserted = $this->db->insert('lms_question_options', [
                    'question_id' => $questionId,
                    'option_text' => trim((string) ($option['text'] ?? '')),
                    'is_correct'  => (int) ($option['is_correct'] ?? 0),
                    'order_num'   => $i,
                ]);

                if (!$inserted) {
                    throw new \RuntimeException('No se pudo insertar una opción.');
                }
            }

            $pdo->commit();
            return (int) $questionId;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log('createQuestionWithOptions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un quiz con todo su contenido, en una transacción.
     *
     * lms_questions y lms_question_options no tienen clave foránea hacia el
     * quiz, así que hay que borrarlas explícitamente o quedan huérfanas.
     * Las respuestas se borran antes que las preguntas porque la FK
     * fk_lms_answers_question es NO ACTION y bloquearía el borrado.
     * Los intentos los arrastra la FK del propio quiz (CASCADE).
     *
     * @return bool
     */
    public function deleteQuizWithContent($quizId): bool
    {
        $quizId = (int) $quizId;
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $pdo->prepare(
                "DELETE FROM lms_quiz_answers
                 WHERE question_id IN (SELECT id FROM lms_questions WHERE quiz_id = ?)"
            )->execute([$quizId]);

            $pdo->prepare(
                "DELETE FROM lms_question_options
                 WHERE question_id IN (SELECT id FROM lms_questions WHERE quiz_id = ?)"
            )->execute([$quizId]);

            $pdo->prepare("DELETE FROM lms_questions WHERE quiz_id = ?")
                ->execute([$quizId]);

            // Arrastra lms_quiz_attempts y, por cascada, sus respuestas.
            $pdo->prepare("DELETE FROM lms_quizzes WHERE id = ?")
                ->execute([$quizId]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log('deleteQuizWithContent: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Resumen de lo que se perdería al borrar un quiz.
     */
    public function quizContentCount($quizId): array
    {
        $quizId = (int) $quizId;
        $row = $this->db->fetchOne(
            "SELECT
               (SELECT COUNT(*) FROM lms_questions WHERE quiz_id = ?) AS preguntas,
               (SELECT COUNT(*) FROM lms_quiz_attempts WHERE quiz_id = ?) AS intentos",
            [$quizId, $quizId]
        );
        return [
            'preguntas' => (int) ($row['preguntas'] ?? 0),
            'intentos'  => (int) ($row['intentos'] ?? 0),
        ];
    }

    /**
     * Cuántos alumnos han respondido ya esta pregunta.
     */
    public function questionAnswerCount($questionId): int
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) AS c FROM lms_quiz_answers WHERE question_id = ?",
            [(int) $questionId]
        );
        return (int) ($row['c'] ?? 0);
    }

    /**
     * Elimina una pregunta, sus opciones y las respuestas de alumnos que la
     * referencian, todo en una transacción.
     *
     * El borrado de las respuestas es imprescindible: la FK
     * fk_lms_answers_question es NO ACTION y bloquearía el borrado. Implica
     * perder el detalle por pregunta de los intentos ya realizados; la
     * calificación guardada en lms_quiz_attempts (score, passed) no cambia.
     *
     * @return bool
     */
    public function deleteQuestionWithOptions($questionId): bool
    {
        $questionId = (int) $questionId;
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $pdo->prepare("DELETE FROM lms_quiz_answers WHERE question_id = ?")
                ->execute([$questionId]);

            $pdo->prepare("DELETE FROM lms_question_options WHERE question_id = ?")
                ->execute([$questionId]);

            $pdo->prepare("DELETE FROM lms_questions WHERE id = ?")
                ->execute([$questionId]);

            $pdo->commit();
            return true;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log('deleteQuestionWithOptions: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Start a new quiz attempt.
     */
    public function startAttempt($studentId, $quizId)
    {
        return $this->db->insert('lms_quiz_attempts', [
            'student_id' => (int) $studentId,
            'quiz_id'    => (int) $quizId,
            'started_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Si un estudiante puede presentarse a un quiz, y por qué no.
     *
     * Sólo cuentan los intentos terminados: startAttempt() crea la fila
     * antes de calificar, así que una calificación que se quede a medias
     * dejaría una fila sin completar que no debe gastarle el intento.
     *
     * El tope sale de la configuración ('lms.max_quiz_attempts'); 0 lo
     * desactiva. Aprobar cierra el examen aunque queden intentos.
     *
     * @return array{realizados:int,limite:int,restantes:?int,aprobado:bool,
     *               bloqueado:bool,motivo:?string,mensaje:?string}
     */
    public function estadoIntentos(int $studentId, int $quizId): array
    {
        $limite = (int) \Core\Config::get('lms.max_quiz_attempts', 3);

        $fila = $this->db->fetchOne(
            'SELECT COUNT(*) AS realizados, COALESCE(MAX(passed), 0) AS aprobado
               FROM lms_quiz_attempts
              WHERE student_id = ? AND quiz_id = ? AND completed_at IS NOT NULL',
            [$studentId, $quizId]
        );

        $realizados = (int) ($fila['realizados'] ?? 0);
        $aprobado   = (int) ($fila['aprobado'] ?? 0) === 1;

        $motivo = null;
        $mensaje = null;
        if ($aprobado) {
            $motivo  = 'aprobado';
            $mensaje = 'Ya aprobaste esta evaluación; no admite nuevos intentos.';
        } elseif ($limite > 0 && $realizados >= $limite) {
            $motivo  = 'sin_intentos';
            $mensaje = sprintf(
                'Has agotado los %d intentos permitidos para esta evaluación.',
                $limite
            );
        }

        return [
            'realizados' => $realizados,
            'limite'     => $limite,
            'restantes'  => $limite > 0 ? max(0, $limite - $realizados) : null,
            'aprobado'   => $aprobado,
            'bloqueado'  => $motivo !== null,
            'motivo'     => $motivo,
            'mensaje'    => $mensaje,
        ];
    }

    /**
     * Grade an attempt based on student answers.
     */
    /**
     * Califica un intento.
     *
     * @param array      $answers  [question_id => option_id]
     * @param int[]|null $asked    Preguntas que se le presentaron al
     *   estudiante. Se usa cuando el quiz sortea un subconjunto del banco:
     *   sin esto se puntuaría sobre las 20 del banco habiendo preguntado
     *   10, y nadie podría pasar del 50%. Null = se preguntaron todas.
     */
    public function gradeAttempt($attemptId, $quizId, array $answers, ?array $asked = null)
    {
        $sql    = "SELECT q.id, q.points,
                          (SELECT id FROM lms_question_options WHERE question_id = q.id AND is_correct = 1 LIMIT 1) AS correct_option_id
                   FROM lms_questions q WHERE q.quiz_id = ?";
        $params = [$quizId];

        // Los ids llegan del navegador: se filtran contra el quiz, de modo
        // que nadie pueda recortar su examen enviando una lista más corta.
        $asked = array_values(array_unique(array_filter(array_map('intval', $asked ?? []))));
        if ($asked) {
            $sql     .= ' AND q.id IN (' . implode(',', array_fill(0, count($asked), '?')) . ')';
            $params   = array_merge($params, $asked);
        }

        $questions = $this->db->fetchAll($sql, $params);

        // Si el filtro no dejó ninguna pregunta válida, se califica sobre
        // todo el quiz antes que dar por bueno un examen vacío.
        if (!$questions) {
            $questions = $this->db->fetchAll(
                "SELECT q.id, q.points,
                        (SELECT id FROM lms_question_options WHERE question_id = q.id AND is_correct = 1 LIMIT 1) AS correct_option_id
                 FROM lms_questions q WHERE q.quiz_id = ?",
                [$quizId]
            );
        }

        $totalPoints  = 0;
        $earnedPoints = 0;

        foreach ($questions as $q) {
            $totalPoints += (float)$q['points'];
            $selectedId   = (int) ($answers[$q['id']] ?? 0);
            $isCorrect    = $selectedId === (int) $q['correct_option_id'];

            if ($isCorrect) $earnedPoints += (float)$q['points'];

            $sql = "INSERT INTO lms_quiz_answers (attempt_id, question_id, selected_option_id, is_correct)
                    VALUES (?, ?, ?, ?)";
            $this->db->query($sql, [$attemptId, $q['id'], $selectedId ?: null, (int) $isCorrect]);
        }

        $quiz = $this->find($quizId);
        $score = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $passed = $score >= (float) ($quiz['pass_percentage'] ?? 70);

        $this->db->update('lms_quiz_attempts', [
            'score' => round($score, 2),
            'total_points' => $totalPoints,
            'passed' => (int) $passed,
            'completed_at' => date('Y-m-d H:i:s')
        ], 'id = :id', ['id' => $attemptId]);

        return [
            'score'        => round($score, 2),
            'earned'       => $earnedPoints,
            'total'        => $totalPoints,
            'passed'       => $passed,
            'pass_score'   => $quiz['pass_percentage'],
        ];
    }

    /**
     * Get the student's best attempt for a specific quiz.
     */
    public function getBestAttempt($studentId, $quizId)
    {
        return $this->db->fetchOne(
            "SELECT * FROM lms_quiz_attempts
             WHERE student_id = ? AND quiz_id = ? AND completed_at IS NOT NULL
             ORDER BY score DESC LIMIT 1",
            [$studentId, $quizId]
        );
    }

    /**
     * Get a specific attempt with all its answers and correct answers.
     */
    public function getAttemptWithAnswers($attemptId)
    {
        $attempt = $this->db->fetchOne(
            "SELECT * FROM lms_quiz_attempts WHERE id = ?",
            [$attemptId]
        );
        if (!$attempt) return false;

        $attempt['answers'] = $this->db->fetchAll(
            "SELECT qa.*, q.question, qo.option_text AS selected_text,
                    (SELECT option_text FROM lms_question_options WHERE question_id = q.id AND is_correct = 1 LIMIT 1) AS correct_text
             FROM lms_quiz_answers qa
             JOIN lms_questions q ON q.id = qa.question_id
             LEFT JOIN lms_question_options qo ON qo.id = qa.selected_option_id
             WHERE qa.attempt_id = ?",
            [$attemptId]
        );

        return $attempt;
    }
}
