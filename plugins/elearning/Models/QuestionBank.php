<?php

namespace Plugins\Elearning\Models;

use Core\Model;

/**
 * Preguntas cuyo quiz_id no corresponde a ningún quiz existente.
 *
 * Son restos de la importación masiva: quedaron agrupadas por su quiz_id
 * original (aquí lo llamamos "set"), pero ese quiz nunca llegó a crearse.
 * Este modelo permite listarlas, reasignarlas a un quiz real o eliminarlas.
 */
class QuestionBank extends Model
{
    protected $table = 'lms_questions';

    /** Condición que identifica una pregunta sin quiz. */
    private const HUERFANA = 'NOT EXISTS (SELECT 1 FROM lms_quizzes z WHERE z.id = q.quiz_id)';

    /**
     * Resumen global: cuántos sets y cuántas preguntas quedan sin asignar.
     */
    public function summary(): array
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) AS preguntas, COUNT(DISTINCT q.quiz_id) AS sets
             FROM lms_questions q WHERE " . self::HUERFANA
        );
        return [
            'preguntas' => (int) ($row['preguntas'] ?? 0),
            'sets'      => (int) ($row['sets'] ?? 0),
        ];
    }

    /**
     * Cuántos sets hay (para paginar).
     */
    public function countSets(string $search = ''): int
    {
        [$extra, $params] = $this->searchClause($search);

        $row = $this->db->fetchOne(
            "SELECT COUNT(*) AS t FROM (
                SELECT q.quiz_id FROM lms_questions q
                WHERE " . self::HUERFANA . " {$extra}
                GROUP BY q.quiz_id
             ) s",
            $params
        );
        return (int) ($row['t'] ?? 0);
    }

    /**
     * Sets con su tamaño y cuántas de sus preguntas ya existen en un quiz vivo.
     */
    public function sets(int $limit, int $offset, string $search = ''): array
    {
        [$extra, $params] = $this->searchClause($search);

        $limit  = max(1, $limit);
        $offset = max(0, $offset);

        return $this->db->fetchAll(
            "SELECT q.quiz_id AS set_id,
                    COUNT(*) AS total,
                    SUM(CASE WHEN EXISTS (
                        SELECT 1 FROM lms_questions v
                        JOIN lms_quizzes zv ON zv.id = v.quiz_id
                        WHERE v.question = q.question
                    ) THEN 1 ELSE 0 END) AS duplicadas
             FROM lms_questions q
             WHERE " . self::HUERFANA . " {$extra}
             GROUP BY q.quiz_id
             ORDER BY COUNT(*) DESC, q.quiz_id ASC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }

    /**
     * Preguntas de un set, con sus opciones.
     */
    public function questions(int $setId, ?int $limit = null): array
    {
        $sql = "SELECT q.id, q.question, q.points, q.order_num
                FROM lms_questions q
                WHERE " . self::HUERFANA . " AND q.quiz_id = ?
                ORDER BY q.order_num ASC, q.id ASC";
        if ($limit !== null) {
            $sql .= ' LIMIT ' . max(1, $limit);
        }

        $questions = $this->db->fetchAll($sql, [$setId]);
        if (!$questions) {
            return [];
        }

        $ids = implode(',', array_map(static fn($r) => (int) $r['id'], $questions));
        $opts = $this->db->fetchAll(
            "SELECT question_id, option_text, is_correct
             FROM lms_question_options
             WHERE question_id IN ({$ids})
             ORDER BY question_id, order_num, id"
        );

        $byQuestion = [];
        foreach ($opts as $o) {
            $byQuestion[(int) $o['question_id']][] = $o;
        }
        foreach ($questions as &$q) {
            $q['options'] = $byQuestion[(int) $q['id']] ?? [];
        }

        return $questions;
    }

    /**
     * Reasigna todas las preguntas de un set a un quiz existente.
     *
     * Los order_num se recalculan a continuación de los que ya tenga el quiz
     * destino, para que no queden posiciones repetidas.
     *
     * @return int|false Preguntas movidas, o false si algo falló.
     */
    public function assignSet(int $setId, int $targetQuizId)
    {
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $exists = $this->db->fetchOne("SELECT id FROM lms_quizzes WHERE id = ?", [$targetQuizId]);
            if (!$exists) {
                throw new \RuntimeException("El quiz destino {$targetQuizId} no existe.");
            }

            $ids = array_map(
                static fn($r) => (int) $r['id'],
                $this->db->fetchAll(
                    "SELECT q.id FROM lms_questions q
                     WHERE " . self::HUERFANA . " AND q.quiz_id = ?
                     ORDER BY q.order_num ASC, q.id ASC",
                    [$setId]
                )
            );
            if (!$ids) {
                throw new \RuntimeException("El set {$setId} no tiene preguntas sin asignar.");
            }

            $maxRow = $this->db->fetchOne(
                "SELECT COALESCE(MAX(order_num), 0) AS m FROM lms_questions WHERE quiz_id = ?",
                [$targetQuizId]
            );
            $order = (int) ($maxRow['m'] ?? 0);

            $stmt = $pdo->prepare("UPDATE lms_questions SET quiz_id = ?, order_num = ? WHERE id = ?");
            foreach ($ids as $id) {
                $stmt->execute([$targetQuizId, ++$order, $id]);
            }

            $pdo->commit();
            return count($ids);
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log('QuestionBank::assignSet: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un set completo: respuestas, opciones y preguntas.
     *
     * @return int|false Preguntas eliminadas, o false si algo falló.
     */
    public function deleteSet(int $setId)
    {
        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $ids = array_map(
                static fn($r) => (int) $r['id'],
                $this->db->fetchAll(
                    "SELECT q.id FROM lms_questions q
                     WHERE " . self::HUERFANA . " AND q.quiz_id = ?",
                    [$setId]
                )
            );
            if (!$ids) {
                throw new \RuntimeException("El set {$setId} no tiene preguntas sin asignar.");
            }

            $in = implode(',', $ids);
            $pdo->exec("DELETE FROM lms_quiz_answers      WHERE question_id IN ({$in})");
            $pdo->exec("DELETE FROM lms_question_options  WHERE question_id IN ({$in})");
            $pdo->exec("DELETE FROM lms_questions         WHERE id IN ({$in})");

            $pdo->commit();
            return count($ids);
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log('QuestionBank::deleteSet: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cuántas respuestas de alumnos se perderían al eliminar un set.
     */
    public function answerCount(int $setId): int
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) AS c FROM lms_quiz_answers a
             WHERE a.question_id IN (
                SELECT q.id FROM lms_questions q
                WHERE " . self::HUERFANA . " AND q.quiz_id = ?
             )",
            [$setId]
        );
        return (int) ($row['c'] ?? 0);
    }

    /**
     * Quizzes disponibles como destino, agrupados por curso.
     * Los que no tienen preguntas se marcan para que sean fáciles de detectar.
     */
    public function targetQuizzes(?int $teacherId = null): array
    {
        $where  = '1=1';
        $params = [];
        if ($teacherId !== null) {
            $where  = 'c.teacher_id = ?';
            $params = [$teacherId];
        }

        return $this->db->fetchAll(
            "SELECT z.id, z.title, c.title AS course_title,
                    (SELECT COUNT(*) FROM lms_questions q WHERE q.quiz_id = z.id) AS question_count
             FROM lms_quizzes z
             JOIN lms_courses c ON c.id = z.course_id
             WHERE {$where}
             ORDER BY c.title ASC, z.title ASC",
            $params
        );
    }

    /**
     * Fragmento de búsqueda por texto de la pregunta.
     *
     * @return array{0:string,1:array}
     */
    private function searchClause(string $search): array
    {
        $search = trim($search);
        if ($search === '') {
            return ['', []];
        }
        // Permite buscar por número de set además de por texto
        if (ctype_digit($search)) {
            return [' AND (q.quiz_id = ? OR q.question LIKE ?)', [(int) $search, "%{$search}%"]];
        }
        return [' AND q.question LIKE ?', ["%{$search}%"]];
    }
}
