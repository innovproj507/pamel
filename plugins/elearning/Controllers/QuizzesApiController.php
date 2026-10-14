<?php

declare(strict_types=1);

namespace Plugins\Elearning\Controllers;

use Core\Controller;
use Core\ApiToken;
use Plugins\Elearning\Models\Quiz;

class QuizzesApiController extends Controller
{
    private Quiz $quizModel;

    public function __construct()
    {
        parent::__construct();
        $this->quizModel = new Quiz();
    }

    /**
     * POST /api/v1/quizzes
     * Body: { course_id, title, description, pass_percentage, time_limit }
     */
    public function store()
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (empty($data['course_id']) || empty($data['title'])) {
            $this->apiResponse(['status' => 'error', 'message' => 'course_id y title son requeridos'], 400);
        }

        $row = [
            'course_id'       => (int) $data['course_id'],
            'title'           => trim($data['title']),
            'description'     => $data['description'] ?? '',
            'pass_percentage' => (float) ($data['pass_percentage'] ?? 70),
            'time_limit'      => (int) ($data['time_limit'] ?? 0),
            'is_active'       => 1,
        ];
        if (!empty($data['lesson_id'])) {
            $row['lesson_id'] = (int) $data['lesson_id'];
        }

        $id = $this->db->insert('lms_quizzes', $row);

        if (!$id) {
            $this->apiResponse(['status' => 'error', 'message' => 'Error al crear quiz'], 500);
        }

        $this->apiResponse(['status' => 'success', 'data' => ['id' => $id]], 201);
    }

    /**
     * GET /api/v1/quizzes/{id}
     */
    public function show($id)
    {
        $quiz = $this->quizModel->find((int) $id);
        if (!$quiz) {
            $this->apiResponse(['status' => 'error', 'message' => 'Quiz no encontrado'], 404);
        }
        $this->apiResponse(['status' => 'success', 'data' => $quiz]);
    }

    /**
     * GET /api/v1/quizzes/{id}/questions
     * Returns quiz with all questions and their options.
     */
    public function showWithQuestions($id)
    {
        $quiz = $this->quizModel->withQuestionsAndOptions((int) $id);
        if (!$quiz) {
            $this->apiResponse(['status' => 'error', 'message' => 'Quiz no encontrado'], 404);
        }
        $this->apiResponse(['status' => 'success', 'data' => $quiz]);
    }

    /**
     * POST /api/v1/quizzes/{id}/update
     * Body: { title?, description?, pass_percentage?, time_limit? }
     */
    public function update($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $quiz = $this->quizModel->find((int) $id);
        if (!$quiz) {
            $this->apiResponse(['status' => 'error', 'message' => 'Quiz no encontrado'], 404);
        }

        $data   = json_decode(file_get_contents('php://input'), true) ?? [];
        $update = [];

        if (isset($data['title']))           $update['title']           = trim($data['title']);
        if (isset($data['description']))     $update['description']     = $data['description'];
        if (isset($data['pass_percentage'])) $update['pass_percentage'] = (float) $data['pass_percentage'];
        if (isset($data['time_limit']))      $update['time_limit']      = (int) $data['time_limit'];

        if (!empty($update)) {
            $this->db->update('lms_quizzes', $update, 'id = :id', ['id' => (int) $id]);
        }

        $this->apiResponse(['status' => 'success', 'message' => 'Quiz actualizado']);
    }

    /**
     * POST /api/v1/quizzes/{id}/delete
     */
    public function destroy($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $quiz = $this->quizModel->find((int) $id);
        if (!$quiz) {
            $this->apiResponse(['status' => 'error', 'message' => 'Quiz no encontrado'], 404);
        }

        // El borrado en cascada (respuestas → intentos → opciones →
        // preguntas → quiz) vive en el modelo y va en una transacción: si
        // algo falla a mitad, no queda el quiz medio borrado.
        if (!$this->quizModel->deleteQuizWithContent((int) $id)) {
            $this->apiResponse(['status' => 'error', 'message' => 'No se pudo eliminar el quiz'], 500);
        }

        $this->apiResponse(['status' => 'success', 'message' => 'Quiz eliminado']);
    }

    /**
     * POST /api/v1/quizzes/{id}/start
     * Starts a new attempt for the authenticated user. Returns attempt ID.
     */
    public function startAttempt($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $quiz = $this->quizModel->find((int) $id);
        if (!$quiz) {
            $this->apiResponse(['status' => 'error', 'message' => 'Quiz no encontrado'], 404);
        }

        $attemptId = $this->quizModel->startAttempt($userId, (int) $id);
        if (!$attemptId) {
            $this->apiResponse(['status' => 'error', 'message' => 'Error al iniciar intento'], 500);
        }

        $this->apiResponse(['status' => 'success', 'data' => (int) $attemptId]);
    }

    /**
     * GET /api/v1/quizzes/{id}/attempts/best
     * Returns the best attempt for the authenticated user.
     */
    public function bestAttempt($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $attempt = $this->quizModel->getBestAttempt($userId, (int) $id);
        $this->apiResponse(['status' => 'success', 'data' => $attempt ?: null]);
    }

    /**
     * POST /api/v1/quizzes/{id}/attempts/{attemptId}/grade
     * Body: { answers: { question_id: option_id, ... } }
     */
    public function gradeAttempt($id, $attemptId)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $data    = json_decode(file_get_contents('php://input'), true) ?? [];
        $answers = array_map('intval', $data['answers'] ?? []);

        // Preguntas realmente presentadas, cuando el quiz sortea un
        // subconjunto del banco. El modelo las valida contra el quiz.
        $asked = isset($data['asked']) && is_array($data['asked'])
            ? array_map('intval', $data['asked'])
            : null;

        $result = $this->quizModel->gradeAttempt((int) $attemptId, (int) $id, $answers, $asked);
        $this->apiResponse(['status' => 'success', 'data' => $result]);
    }

    /**
     * GET /api/v1/quiz-attempts/{attemptId}
     * Returns attempt with all answers and correct answers.
     */
    public function attempt($attemptId)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $attempt = $this->quizModel->getAttemptWithAnswers((int) $attemptId);
        if (!$attempt) {
            $this->apiResponse(['status' => 'error', 'message' => 'Intento no encontrado'], 404);
        }

        $this->apiResponse(['status' => 'success', 'data' => $attempt]);
    }

    /**
     * POST /api/v1/quizzes/{id}/questions
     * Body: { question, type, points, order_num, options: [{text, is_correct}] }
     */
    public function storeQuestion($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $data    = json_decode(file_get_contents('php://input'), true) ?? [];
        $options = $data['options'] ?? [];

        $qId = $this->db->insert('lms_questions', [
            'quiz_id'   => (int) $id,
            'question'  => trim($data['question'] ?? ''),
            'type'      => $data['type'] ?? 'multiple_choice',
            'points'    => (int) ($data['points'] ?? 1),
            'order_num' => (int) ($data['order_num'] ?? 0),
        ]);

        if (!$qId) {
            $this->apiResponse(['status' => 'error', 'message' => 'Error al crear pregunta'], 500);
        }

        foreach ($options as $i => $opt) {
            $this->db->insert('lms_question_options', [
                'question_id' => $qId,
                'option_text' => trim($opt['text'] ?? ''),
                'is_correct'  => (int) ($opt['is_correct'] ?? 0),
                'order_num'   => $i,
            ]);
        }

        $this->apiResponse(['status' => 'success', 'data' => ['id' => $qId]], 201);
    }

    /**
     * GET /api/v1/lessons/{id}/quiz
     * Returns the quiz (with questions+options) attached to the lesson, or null.
     */
    public function lessonQuiz($id)
    {
        $quiz = $this->quizModel->findByLesson((int) $id);
        if (!$quiz) {
            $this->apiResponse(['status' => 'success', 'data' => null]);
        }
        $full = $this->quizModel->withQuestionsAndOptions((int) $quiz['id']);
        $this->apiResponse(['status' => 'success', 'data' => $full ?: $quiz]);
    }

    /**
     * POST /api/v1/questions/:id/delete
     */
    public function deleteQuestion($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        // Arrastra también las respuestas ya dadas por los estudiantes, en
        // una transacción. Borrar sólo opciones y pregunta dejaba respuestas
        // apuntando a una pregunta inexistente.
        if (!$this->quizModel->deleteQuestionWithOptions((int) $id)) {
            $this->apiResponse(['status' => 'error', 'message' => 'No se pudo eliminar la pregunta'], 500);
        }

        $this->apiResponse(['status' => 'success', 'message' => 'Pregunta eliminada']);
    }

    private function apiResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
