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
     * Grade an attempt based on student answers.
     */
    public function gradeAttempt($attemptId, $quizId, array $answers)
    {
        $questions = $this->db->fetchAll(
            "SELECT q.id, q.points,
                    (SELECT id FROM lms_question_options WHERE question_id = q.id AND is_correct = 1 LIMIT 1) AS correct_option_id
             FROM lms_questions q WHERE q.quiz_id = ?",
            [$quizId]
        );

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
