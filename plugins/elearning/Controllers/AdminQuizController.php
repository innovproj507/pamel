<?php

namespace Plugins\Elearning\Controllers;

use Core\View;
use Plugins\Elearning\Models\Quiz;
use Plugins\Elearning\Models\Course;

class AdminQuizController extends BaseController
{
    private $quizModel;
    private $courseModel;

    public function __construct()
    {
        parent::__construct();
        $this->quizModel = new Quiz();
        $this->courseModel = new Course();
    }

    /**
     * List all quizzes.
     */
    public function index()
    {
        $this->requireAuth();
        
        $view = new View();
        $quizzes = $this->db->fetchAll(
            "SELECT q.*, c.title as course_title,
                    (SELECT COUNT(*) FROM lms_questions WHERE quiz_id = q.id) as question_count
             FROM lms_quizzes q
             LEFT JOIN lms_courses c ON c.id = q.course_id
             ORDER BY q.created_at DESC"
        );

        $view->render('admin/views/lms/quizzes/index', [
            'title'   => 'Gestión de Quizzes',
            'quizzes' => $quizzes,
        ], 'admin/views/layout');
    }

    /**
     * Show form to create a new quiz.
     */
    public function create()
    {
        $this->requireAuth();
        
        $view = new View();
        $courses = $this->db->fetchAll("SELECT id, title FROM lms_courses ORDER BY title ASC");

        $view->render('admin/views/lms/quizzes/create', [
            'title'   => 'Crear Nuevo Quiz',
            'courses' => $courses,
        ], 'admin/views/layout');
    }

    /**
     * Store a new quiz.
     */
    public function store()
    {
        $this->requireAuth();
        $this->validateCsrf('/manager/lms/quizzes');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/quizzes');
        }

        $data = [
            'course_id'       => (int)($_POST['course_id'] ?? 0),
            'title'           => $_POST['title'] ?? '',
            'description'     => $_POST['description'] ?? '',
            'pass_percentage' => (float)($_POST['pass_percentage'] ?? 70),
            'time_limit'      => (int)($_POST['time_limit'] ?? 0),
            'is_active'       => isset($_POST['is_active']) ? 1 : 0,
            'created_at'      => date('Y-m-d H:i:s')
        ];

        $this->db->insert('lms_quizzes', $data);
        
        $this->flash('success', 'Quiz creado correctamente.');
        $this->redirect('/manager/lms/quizzes');
    }

    /**
     * Show form to edit a quiz.
     */
    public function edit($id)
    {
        $this->requireAuth();
        
        $quiz = $this->quizModel->find($id);
        if (!$quiz) {
            $this->redirect('/manager/lms/quizzes');
        }

        $view = new View();
        $courses = $this->db->fetchAll("SELECT id, title FROM lms_courses ORDER BY title ASC");

        $view->render('admin/views/lms/quizzes/edit', [
            'title'   => 'Editar Quiz: ' . $quiz['title'],
            'quiz'    => $quiz,
            'courses' => $courses,
        ], 'admin/views/layout');
    }

    /**
     * Update an existing quiz.
     */
    public function update($id)
    {
        $this->requireAuth();
        $this->validateCsrf('/manager/lms/quizzes');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/quizzes');
        }

        $data = [
            'course_id'       => (int)($_POST['course_id'] ?? 0),
            'title'           => $_POST['title'] ?? '',
            'description'     => $_POST['description'] ?? '',
            'pass_percentage' => (float)($_POST['pass_percentage'] ?? 70),
            'time_limit'      => (int)($_POST['time_limit'] ?? 0),
            'is_active'       => isset($_POST['is_active']) ? 1 : 0,
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        $this->db->update('lms_quizzes', $data, 'id = :id', ['id' => $id]);
        
        $this->flash('success', 'Quiz actualizado correctamente.');
        $this->redirect('/manager/lms/quizzes');
    }

    /**
     * Delete a quiz.
     */
    public function delete($id)
    {
        $this->requireAuth();
        $this->validateCsrf('/manager/lms/quizzes');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/quizzes');
        }

        $this->db->query("DELETE FROM lms_quizzes WHERE id = ?", [$id]);
        
        $this->flash('success', 'Quiz eliminado correctamente.');
        $this->redirect('/manager/lms/quizzes');
    }

    // ─── Question Management ──────────────────────────────────────

    /**
     * List all questions for a specific quiz.
     */
    public function questions($quizId)
    {
        $this->requireAuth();
        
        $quiz = $this->quizModel->find($quizId);
        if (!$quiz) {
            $this->redirect('/manager/lms/quizzes');
        }

        $view = new View();
        $questions = $this->db->fetchAll(
            "SELECT q.*, (SELECT COUNT(*) FROM lms_question_options WHERE question_id = q.id) as option_count
             FROM lms_questions q
             WHERE q.quiz_id = ?
             ORDER BY q.order_num ASC",
            [$quizId]
        );

        $view->render('admin/views/lms/quizzes/questions', [
            'title'     => 'Preguntas: ' . $quiz['title'],
            'quiz'      => $quiz,
            'questions' => $questions,
        ], 'admin/views/layout');
    }

    /**
     * Show form to add a new question.
     */
    public function addQuestion($quizId)
    {
        $this->requireAuth();
        
        $quiz = $this->quizModel->find($quizId);
        if (!$quiz) {
            $this->redirect('/manager/lms/quizzes');
        }

        $view = new View();
        $view->render('admin/views/lms/quizzes/add_question', [
            'title' => 'Añadir Pregunta',
            'quiz'  => $quiz,
        ], 'admin/views/layout');
    }

    /**
     * Store a new question with options.
     */
    public function storeQuestion($quizId)
    {
        $this->requireAuth();
        $this->validateCsrf("/manager/lms/quizzes/{$quizId}/questions/add");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("/manager/lms/quizzes/{$quizId}/questions");
        }

        $questionText = $_POST['question'] ?? '';
        $points = (int)($_POST['points'] ?? 1);
        $orderNum = (int)($_POST['order_num'] ?? 0);
        
        $options = [];
        $optionTexts = $_POST['options'] ?? [];
        $correctIndex = (int)($_POST['correct_option'] ?? 0);

        foreach ($optionTexts as $index => $text) {
            if (empty(trim($text))) continue;
            $options[] = [
                'text'       => $text,
                'is_correct' => ($index === $correctIndex) ? 1 : 0
            ];
        }

        if (empty($options)) {
            $this->flash('error', 'Debes añadir al menos una opción.');
            $this->redirect("/manager/lms/quizzes/{$quizId}/questions/add");
        }

        // Use the model's helper
        $this->quizModel->createQuestionWithOptions([
            'quiz_id'   => $quizId,
            'question'  => $questionText,
            'points'    => $points,
            'order_num' => $orderNum
        ], $options);

        $this->flash('success', 'Pregunta añadida correctamente.');
        $this->redirect("/manager/lms/quizzes/{$quizId}/questions");
    }

    /**
     * Delete a question.
     */
    public function deleteQuestion($quizId, $id)
    {
        $this->requireAuth();
        $this->validateCsrf("/manager/lms/quizzes/{$quizId}/questions");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("/manager/lms/quizzes/{$quizId}/questions");
        }

        // Options are deleted by CASCADE in DB (hopefully)
        // If not, we manually delete them
        $this->db->query("DELETE FROM lms_question_options WHERE question_id = ?", [$id]);
        $this->db->query("DELETE FROM lms_questions WHERE id = ?", [$id]);
        
        $this->flash('success', 'Pregunta eliminada.');
        $this->redirect("/manager/lms/quizzes/{$quizId}/questions");
    }
}
