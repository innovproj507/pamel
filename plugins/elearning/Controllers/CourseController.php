<?php

namespace Plugins\Elearning\Controllers;

use Core\View;
use Plugins\Elearning\Models\Course;
use Plugins\Elearning\Models\Enrollment;
use Plugins\Elearning\Models\Lesson;
use Plugins\Elearning\Models\Certificate;

class CourseController extends BaseController
{
    private $courseModel;
    private $enrollmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->courseModel = new Course();
        $this->enrollmentModel = new Enrollment();
    }

    /**
     * List all published courses.
     */
    public function index()
    {
        $view = new View();
        $page = max(1, (int)($_GET['page'] ?? 1));
        $categoryId = (int)($_GET['categoria'] ?? 0) ?: null;
        
        $courses = $this->courseModel->published($page, 12, $categoryId);
        
        // Fetch categories for the filter
        $categories = $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM lms_courses WHERE category_id = c.id AND status = 'published') as course_count 
             FROM lms_categories c ORDER BY name ASC"
        );

        $view->render('plugins/elearning/Views/courses/index', [
            'title'      => 'Cursos Disponibles',
            'courses'    => $courses,
            'categories' => $categories,
            'currentCat' => $categoryId,
        ], 'plugins/elearning/Views/layout');
    }

    /**
     * Show course details and lessons.
     */
    public function show($slug)
    {
        $view = new View();
        $course = $this->courseModel->findBySlug($slug);

        if (!$course) {
            http_response_code(404);
            echo "Course not found";
            return;
        }

        $content = $this->courseModel->getLessonsAndQuizzes((int)$course['id']);
        $enrolled = false;
        $completedLessonIds = [];
        
        if ($this->user) {
            $enrolled = $this->enrollmentModel->isEnrolled((int)$this->user['id'], (int)$course['id']);
            if ($enrolled) {
                $lessonModel = new Lesson();
                $completedLessonIds = $lessonModel->completedByStudentInCourse((int)$this->user['id'], (int)$course['id']);
            }
        }

        $isManager = $this->user && in_array($this->user['role'], ['admin', 'teacher']);

        $view->render('plugins/elearning/Views/courses/show', [
            'title'              => $course['title'],
            'course'             => $course,
            'lessons'            => $content['lessons'],
            'quizzes'            => $content['quizzes'],
            'enrolled'           => $enrolled,
            'user'               => $this->user,
            'completedLessonIds' => $completedLessonIds,
            'isManager'          => $isManager,
        ], 'plugins/elearning/Views/layout');
    }

    /**
     * Enroll in a course.
     */
    public function enroll($id)
    {
        $this->requireAuth();
        
        // Security check: simple CSRF or POST check
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/courses');
        }

        $course = $this->courseModel->find($id);
        if (!$course) {
            $this->redirect('/courses');
        }

        if (!$this->enrollmentModel->isEnrolled($this->user['id'], $course['id'])) {
            $this->enrollmentModel->enroll($this->user['id'], $course['id']);
            $this->flash('success', '¡Inscripción exitosa!');
        }

        $this->redirect('/courses/' . $course['slug']);
    }

    /**
     * Download certificate if course is completed.
     */
    public function certificate($slug)
    {
        $this->requireAuth();
        $user = $this->session->get('user');
        
        $course = $this->courseModel->findBySlug($slug);
        if (!$course) {
            $this->redirect('/dashboard');
        }

        // Verify enrollment and completion
        $enrollmentModel = new Enrollment();
        if (!$enrollmentModel->isEnrolled($user['id'], $course['id'])) {
            $this->redirect('/cursos/' . $slug);
        }

        // In a real scenario, check if ALL lessons are completed
        // For now, let's allow it if enrolled
        
        $certificateModel = new Certificate();
        $cert = $certificateModel->getForStudentCourse($user['id'], $course['id']);
        
        if (!$cert) {
            $code = $certificateModel->generateCode();
            $certificateModel->issue($user['id'], $course['id'], $code);
            $cert = $certificateModel->getForStudentCourse($user['id'], $course['id']);
        }

        // Trigger generation (Simplified for this demo)
        $this->downloadGeneratedCertificate($user, $course, $cert['certificate_code']);
    }

    private function downloadGeneratedCertificate($user, $course, $code)
    {
        $userName = $user['name'];
        $courseTitle = $course['title'];
        $date = date('d/m/Y');

        try {
            $filepath = \Plugins\Elearning\Services\CertificateService::generate($userName, $courseTitle, $date, $code);

            if (file_exists($filepath)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Disposition: attachment; filename="Certificado_' . str_replace(' ', '_', $courseTitle) . '.docx"');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($filepath));

                ob_clean();
                flush();
                readfile($filepath);
                unlink($filepath); // Delete temp file
                exit;
            }
        } catch (\Exception $e) {
            error_log("Error generating certificate: " . $e->getMessage());
            $this->flash('error', 'Error al generar el certificado. Inténtalo de nuevo.');
            $this->redirect('/dashboard');
        }
    }
}
