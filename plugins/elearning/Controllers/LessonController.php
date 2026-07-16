<?php

namespace Plugins\Elearning\Controllers;

use Core\View;
use Plugins\Elearning\Models\Course;
use Plugins\Elearning\Models\Enrollment;
use Plugins\Elearning\Models\Lesson;

class LessonController extends BaseController
{
    private $lessonModel;
    private $courseModel;
    private $enrollmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->lessonModel = new Lesson();
        $this->courseModel = new Course();
        $this->enrollmentModel = new Enrollment();
    }

    /**
     * Show a specific lesson.
     */
    public function show($courseId, $id)
    {
        $this->requireAuth();
        
        $view = new View();
        $course = $this->courseModel->find($courseId);
        $lesson = $this->lessonModel->find($id);

        if (!$course || !$lesson) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $isManager = in_array($this->user['role'], ['teacher', 'admin']);

        // Check if lesson is active
        if (!$lesson['is_active'] && !$isManager) {
            $this->flash('error', 'Esta lección no está disponible.');
            $this->redirect('/courses/' . $course['slug']);
        }

        // Check enrollment
        if (!$isManager) {
            if (!$this->enrollmentModel->isEnrolled($this->user['id'], $course['id'])) {
                $this->flash('error', 'Debes inscribirte para acceder al contenido.');
                $this->redirect('/courses/' . $course['slug']);
            }

            // Sequential enforcement
            $prev = $this->lessonModel->previousInCourse($course['id'], $lesson['order_num']);
            if ($prev && !$this->lessonModel->isCompleted($this->user['id'], $prev['id'])) {
                $this->flash('error', 'Debes completar la lección anterior: "' . $prev['title'] . '".');
                $this->redirect('/courses/' . $course['slug']);
            }
        }

        $isCompleted = $this->lessonModel->isCompleted($this->user['id'], $lesson['id']);
        $allLessons = $this->lessonModel->byCourse($course['id']);
        $completedLessonIds = $this->lessonModel->completedByStudentInCourse($this->user['id'], $course['id']);
        
        // Quizzes (simple fetch for sidebar)
        $quizzes = $this->db->fetchAll("SELECT * FROM lms_quizzes WHERE course_id = ?", [$course['id']]);

        $view->render('plugins/elearning/Views/lessons/show', [
            'title'              => $lesson['title'],
            'course'             => $course,
            'lesson'             => $lesson,
            'allLessons'         => $allLessons,
            'isCompleted'        => $isCompleted,
            'completedLessonIds' => $completedLessonIds,
            'quizzes'            => $quizzes,
            'isManager'          => $isManager,
        ], 'plugins/elearning/Views/layout');
    }

    /**
     * Mark lesson as completed.
     */
    public function complete($courseId, $id)
    {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/courses');
        }

        $this->lessonModel->markCompleted($this->user['id'], $id);
        
        $course = $this->courseModel->find($courseId);
        $this->flash('success', '¡Lección completada!');
        
        $this->redirect('/courses/' . ($course ? $course['slug'] : $courseId) . '/lessons/' . $id);
    }
}
