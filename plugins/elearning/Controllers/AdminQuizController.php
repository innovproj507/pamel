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
        $this->requireCan('lms.quizzes.view');

        $perPage  = 8;   // cursos por página
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $q        = trim($_GET['q'] ?? '');
        $courseId = (int) ($_GET['course_id'] ?? 0);
        $status   = $_GET['status'] ?? '';
        $empty    = $_GET['empty'] ?? '';

        // Condiciones que filtran los QUIZZES
        $where  = ['1=1'];
        $params = [];

        if ($this->user['role'] === 'teacher') {
            $where[]  = 'c.teacher_id = ?';
            $params[] = (int) $this->user['id'];
        }
        if ($q !== '') {
            $where[]  = '(z.title LIKE ? OR c.title LIKE ?)';
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        }
        if ($courseId > 0) {
            $where[]  = 'c.id = ?';
            $params[] = $courseId;
        }
        if ($status === 'active') {
            $where[] = 'z.is_active = 1';
        } elseif ($status === 'hidden') {
            $where[] = 'z.is_active = 0';
        }
        if ($empty === '1') {
            $where[] = 'NOT EXISTS (SELECT 1 FROM lms_questions qq WHERE qq.quiz_id = z.id)';
        }
        $whereStr = implode(' AND ', $where);

        // Cursos que tienen al menos un quiz que cumple el filtro
        $total = (int) ($this->db->fetchOne(
            "SELECT COUNT(DISTINCT c.id) AS t
             FROM lms_quizzes z
             JOIN lms_courses c ON c.id = z.course_id
             WHERE {$whereStr}",
            $params
        )['t'] ?? 0);

        $offset  = ($page - 1) * $perPage;
        $courses = $this->db->fetchAll(
            "SELECT c.id, c.title, c.status,
                    COUNT(z.id) AS quiz_count,
                    COALESCE(SUM((SELECT COUNT(*) FROM lms_questions qq WHERE qq.quiz_id = z.id)), 0) AS question_total,
                    SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM lms_questions qq WHERE qq.quiz_id = z.id) THEN 1 ELSE 0 END) AS empty_count
             FROM lms_quizzes z
             JOIN lms_courses c ON c.id = z.course_id
             WHERE {$whereStr}
             GROUP BY c.id, c.title, c.status
             ORDER BY c.title ASC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        // Quizzes de los cursos de esta página
        $quizzesByCourse = [];
        if ($courses) {
            $ids  = array_map(static fn($c) => (int) $c['id'], $courses);
            $in   = implode(',', array_fill(0, count($ids), '?'));
            $rows = $this->db->fetchAll(
                "SELECT z.*,
                        (SELECT COUNT(*) FROM lms_questions qq WHERE qq.quiz_id = z.id) AS question_count,
                        (SELECT COUNT(*) FROM lms_quiz_attempts t WHERE t.quiz_id = z.id) AS attempt_count
                 FROM lms_quizzes z
                 JOIN lms_courses c ON c.id = z.course_id
                 WHERE {$whereStr} AND c.id IN ({$in})
                 ORDER BY z.title ASC, z.id ASC",
                array_merge($params, $ids)
            );
            foreach ($rows as $row) {
                $quizzesByCourse[(int) $row['course_id']][] = $row;
            }
        }

        // Resumen global (sin filtros de página, sí con el ámbito del rol)
        $scope       = $this->user['role'] === 'teacher' ? 'c.teacher_id = ?' : '1=1';
        $scopeParams = $this->user['role'] === 'teacher' ? [(int) $this->user['id']] : [];
        $stats = $this->db->fetchOne(
            "SELECT COUNT(*) AS quizzes,
                    COALESCE(SUM((SELECT COUNT(*) FROM lms_questions qq WHERE qq.quiz_id = z.id)), 0) AS preguntas,
                    SUM(CASE WHEN NOT EXISTS (SELECT 1 FROM lms_questions qq WHERE qq.quiz_id = z.id) THEN 1 ELSE 0 END) AS vacios
             FROM lms_quizzes z
             JOIN lms_courses c ON c.id = z.course_id
             WHERE {$scope}",
            $scopeParams
        );

        $courseOptions = $this->user['role'] === 'teacher'
            ? $this->db->fetchAll("SELECT id, title FROM lms_courses WHERE teacher_id = ? ORDER BY title ASC", [(int) $this->user['id']])
            : $this->db->fetchAll("SELECT id, title FROM lms_courses ORDER BY title ASC");

        $view = new View();
        $view->render('admin/views/lms/quizzes/index', [
            'title'           => 'Gestión de Quizzes',
            'courses'         => $courses,
            'quizzesByCourse' => $quizzesByCourse,
            'courseOptions'   => $courseOptions,
            'stats'           => $stats,
            'total'           => $total,
            'page'            => $page,
            'perPage'         => $perPage,
            'lastPage'        => max(1, (int) ceil($total / $perPage)),
            'q'               => $q,
            'filterCourse'    => $courseId,
            'filterStatus'    => $status,
            'filterEmpty'     => $empty,
        ], 'admin/views/layout');
    }

    /**
     * Show form to create a new quiz.
     */
    public function create()
    {
        $this->requireCan('lms.quizzes.manage');

        $view = new View();
        $courses = $this->user['role'] === 'teacher'
            ? $this->db->fetchAll("SELECT id, title FROM lms_courses WHERE teacher_id = ? ORDER BY title ASC", [(int) $this->user['id']])
            : $this->db->fetchAll("SELECT id, title FROM lms_courses ORDER BY title ASC");

        $view->render('admin/views/lms/quizzes/create', [
            'title'            => 'Crear Nuevo Quiz',
            'courses'          => $courses,
            'selectedCourseId' => (int) ($_GET['course_id'] ?? 0),
        ], 'admin/views/layout');
    }

    /**
     * Store a new quiz.
     */
    public function store()
    {
        $this->requireCan('lms.quizzes.manage');
        $this->validateCsrf('/manager/lms/quizzes');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/quizzes');
        }

        $courseId = (int)($_POST['course_id'] ?? 0);
        $this->authorizeCourseOwnership($courseId);

        $data = [
            'course_id'       => $courseId,
            'title'           => $_POST['title'] ?? '',
            'description'     => $_POST['description'] ?? '',
            'pass_percentage' => (float)($_POST['pass_percentage'] ?? 70),
            'time_limit'      => (int)($_POST['time_limit'] ?? 0),
            'is_active'       => isset($_POST['is_active']) ? 1 : 0,
            'created_at'      => date('Y-m-d H:i:s')
        ];

        if (!$this->db->insert('lms_quizzes', $data)) {
            $this->flash('error', 'No se pudo crear el quiz. Revisa el log de errores de PHP.');
            $this->redirect('/manager/lms/quizzes/create?course_id=' . $courseId);
        }

        $this->flash('success', 'Quiz creado correctamente.');
        $this->redirect('/manager/lms/quizzes');
    }

    /**
     * Show form to edit a quiz.
     */
    public function edit($id)
    {
        $this->requireCan('lms.quizzes.manage');

        $quiz = $this->authorizeQuiz($id);

        $view = new View();
        $courses = $this->user['role'] === 'teacher'
            ? $this->db->fetchAll("SELECT id, title FROM lms_courses WHERE teacher_id = ? ORDER BY title ASC", [(int) $this->user['id']])
            : $this->db->fetchAll("SELECT id, title FROM lms_courses ORDER BY title ASC");

        // find() no trae el conteo; la vista lo necesita para el panel lateral.
        $quiz['question_count'] = (int) ($this->db->fetchOne(
            "SELECT COUNT(*) AS c FROM lms_questions WHERE quiz_id = ?",
            [(int) $quiz['id']]
        )['c'] ?? 0);

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
        $this->requireCan('lms.quizzes.manage');
        $this->validateCsrf('/manager/lms/quizzes');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/quizzes');
        }

        // Must own the quiz being edited, and (if reassigning) the target course too.
        $this->authorizeQuiz($id);
        $courseId = (int)($_POST['course_id'] ?? 0);
        $this->authorizeCourseOwnership($courseId);

        $data = [
            'course_id'       => $courseId,
            'title'           => $_POST['title'] ?? '',
            'description'     => $_POST['description'] ?? '',
            'pass_percentage' => (float)($_POST['pass_percentage'] ?? 70),
            'time_limit'      => (int)($_POST['time_limit'] ?? 0),
            'is_active'       => isset($_POST['is_active']) ? 1 : 0,
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        if ($this->db->update('lms_quizzes', $data, 'id = :id', ['id' => $id]) === false) {
            $this->flash('error', 'No se pudo guardar el quiz. Revisa el log de errores de PHP.');
            $this->redirect("/manager/lms/quizzes/{$id}/edit");
        }

        $this->flash('success', 'Quiz actualizado correctamente.');
        $this->redirect('/manager/lms/quizzes');
    }

    /**
     * Delete a quiz.
     */
    public function delete($id)
    {
        $this->requireCan('lms.quizzes.manage');
        $this->validateCsrf('/manager/lms/quizzes');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/quizzes');
        }

        $this->authorizeQuiz($id);

        // Se cuenta antes para informar qué contenido se llevó por delante.
        $content = $this->quizModel->quizContentCount($id);

        if (!$this->quizModel->deleteQuizWithContent($id)) {
            $this->flash('error', 'No se pudo eliminar el quiz. Revisa el log de errores de PHP.');
            $this->redirect('/manager/lms/quizzes');
        }

        $this->flash('success', sprintf(
            'Quiz eliminado junto con %d pregunta(s) y %d intento(s) de alumnos.',
            $content['preguntas'],
            $content['intentos']
        ));
        $this->redirect('/manager/lms/quizzes');
    }

    // ─── Question Management ──────────────────────────────────────

    /**
     * List all questions for a specific quiz.
     */
    public function questions($quizId)
    {
        $this->requireCan('lms.quizzes.view');

        $quiz = $this->authorizeQuiz($quizId);

        $view = new View();
        $questions = $this->db->fetchAll(
            "SELECT q.*,
                    (SELECT COUNT(*) FROM lms_question_options WHERE question_id = q.id) as option_count,
                    (SELECT COUNT(*) FROM lms_quiz_answers      WHERE question_id = q.id) as answer_count
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
        $this->requireCan('lms.quizzes.manage');

        $quiz = $this->authorizeQuiz($quizId);

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
        $this->requireCan('lms.quizzes.manage');
        $this->validateCsrf("/manager/lms/quizzes/{$quizId}/questions/add");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("/manager/lms/quizzes/{$quizId}/questions");
        }

        $this->authorizeQuiz($quizId);

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

        $created = $this->quizModel->createQuestionWithOptions([
            'quiz_id'   => $quizId,
            'question'  => $questionText,
            'points'    => $points,
            'order_num' => $orderNum
        ], $options);

        if (!$created) {
            $this->flash('error', 'No se pudo guardar la pregunta. Revisa el log de errores de PHP.');
            $this->redirect("/manager/lms/quizzes/{$quizId}/questions/add");
        }

        $this->flash('success', 'Pregunta añadida correctamente.');
        $this->redirect("/manager/lms/quizzes/{$quizId}/questions");
    }

    /**
     * Delete a question.
     */
    public function deleteQuestion($quizId, $id)
    {
        $this->requireCan('lms.quizzes.manage');
        $this->validateCsrf("/manager/lms/quizzes/{$quizId}/questions");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("/manager/lms/quizzes/{$quizId}/questions");
        }

        $this->authorizeQuiz($quizId);

        // Se cuenta antes para poder informar qué historial se destruyó.
        $answers = $this->quizModel->questionAnswerCount($id);

        if (!$this->quizModel->deleteQuestionWithOptions($id)) {
            $this->flash('error', 'No se pudo eliminar la pregunta. Revisa el log de errores de PHP.');
            $this->redirect("/manager/lms/quizzes/{$quizId}/questions");
        }

        $this->flash('success', $answers > 0
            ? sprintf('Pregunta eliminada junto con %d respuesta(s) de alumnos.', $answers)
            : 'Pregunta eliminada.');
        $this->redirect("/manager/lms/quizzes/{$quizId}/questions");
    }

    /**
     * Fetch a quiz and ensure the current user owns the course it belongs to.
     */
    private function authorizeQuiz($quizId)
    {
        $quiz = $this->quizModel->find($quizId);
        if (!$quiz) {
            $this->redirect('/manager/lms/quizzes');
        }
        $this->authorizeCourseOwnership($quiz['course_id']);
        return $quiz;
    }
}
