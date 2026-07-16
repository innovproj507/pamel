<?php

namespace Plugins\Elearning\Controllers;

use Core\View;
use Plugins\Elearning\Models\Quiz;
use Plugins\Elearning\Models\Course;
use Plugins\Elearning\Models\Enrollment;
use Plugins\Elearning\Models\Lesson;

class QuizController extends BaseController
{
    private $quizModel;
    private $courseModel;
    private $enrollmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->quizModel = new Quiz();
        $this->courseModel = new Course();
        $this->enrollmentModel = new Enrollment();
    }

    /**
     * Show quiz for student to answer.
     */
    public function show($courseId, $id)
    {
        $this->requireAuth();
        
        $view = new View();
        $course = $this->courseModel->find($courseId);
        $quiz = $this->quizModel->withQuestionsAndOptions($id);

        if (!$course || !$quiz) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $isManager = in_array($this->user['role'], ['teacher', 'admin']);

        // Check enrollment
        if (!$isManager) {
            if (!$this->enrollmentModel->isEnrolled($this->user['id'], $course['id'])) {
                $this->flash('error', 'Debes estar inscrito para realizar el quiz.');
                $this->redirect('/courses/' . $course['slug']);
            }

            // Prerequisite: complete all lessons
            $lessonModel = new Lesson();
            $allLessons = $lessonModel->byCourse($course['id']);
            $completedIds = $lessonModel->completedByStudentInCourse($this->user['id'], $course['id']);
            
            $pending = array_filter($allLessons, function($l) use ($completedIds) {
                return $l['is_active'] && !in_array((int)$l['id'], $completedIds);
            });

            if (!empty($pending)) {
                $first = reset($pending);
                $this->flash('error', 'Debes completar todas las lecciones antes de realizar el quiz.');
                $this->redirect('/courses/' . $course['slug']);
            }
        }

        $bestAttempt = $this->quizModel->getBestAttempt($this->user['id'], $quiz['id']);

        $view->render('plugins/elearning/Views/quizzes/show', [
            'title'       => $quiz['title'],
            'course'      => $course,
            'quiz'        => $quiz,
            'bestAttempt' => $bestAttempt,
            'user'        => $this->user,
        ], 'plugins/elearning/Views/layout');
    }

    /**
     * Submit quiz answers.
     */
    public function submit($courseId, $id)
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/courses');
        }

        $course = $this->courseModel->find($courseId);
        
        // Start attempt
        $attemptId = $this->quizModel->startAttempt($this->user['id'], $id);

        // Answers
        $answers = $_POST['answers'] ?? [];
        $result = $this->quizModel->gradeAttempt($attemptId, $id, $answers);

        $this->flash('success', sprintf(
            'Quiz completado. Puntuación: %.1f%%. %s',
            $result['score'],
            $result['passed'] ? '¡Aprobado!' : 'No aprobado, puedes intentarlo de nuevo.'
        ));

        $this->redirect("/courses/{$courseId}/quizzes/{$id}/results?attempt={$attemptId}");
    }

    /**
     * Show quiz results.
     */
    public function results($courseId, $id)
    {
        $this->requireAuth();
        
        $view = new View();
        $course = $this->courseModel->find($courseId);
        $quiz = $this->quizModel->find($id);
        $attemptId = (int)($_GET['attempt'] ?? 0);

        $attempt = $this->quizModel->getAttemptWithAnswers($attemptId);

        if (!$attempt || (int)$attempt['student_id'] !== (int)$this->user['id']) {
            $this->redirect('/dashboard');
        }

        $view->render('plugins/elearning/Views/quizzes/results', [
            'title'   => 'Resultados: ' . $quiz['title'],
            'course'  => $course,
            'quiz'    => $quiz,
            'attempt' => $attempt,
            'user'    => $this->user,
        ], 'plugins/elearning/Views/layout');
    }
}
