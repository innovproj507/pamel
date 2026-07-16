<?php

namespace Plugins\Elearning\Controllers;

use Core\View;
use Plugins\Elearning\Models\Enrollment;
use Plugins\Elearning\Models\Course;

class AdminEnrollmentController extends BaseController
{
    private $enrollmentModel;
    private $courseModel;

    public function __construct()
    {
        parent::__construct();
        $this->enrollmentModel = new Enrollment();
        $this->courseModel = new Course();
    }

    /**
     * List all students with their summary and stats.
     */
    public function index()
    {
        $this->requireAuth();
        
        $view = new View();
        
        // Get Student Users with Enrollment count
        $students = $this->db->fetchAll(
            "SELECT u.*, 
                    (SELECT COUNT(*) FROM lms_enrollments WHERE student_id = u.id) as course_count
             FROM users u
             WHERE u.role = 'student'
             ORDER BY u.created_at DESC"
        );

        // Stats
        $stats = [
            'total'  => count($students),
            'active' => count(array_filter($students, fn($s) => $s['status'] === 'active')),
            'blocked'=> count(array_filter($students, fn($s) => $s['status'] === 'inactive')),
        ];

        $view->render('admin/views/lms/students/index', [
            'title'    => 'Gestión de Estudiantes',
            'students' => $students,
            'stats'    => $stats,
        ], 'admin/views/layout');
    }

    /**
     * Show form to manually enroll a student.
     */
    public function create()
    {
        $this->requireAuth();

        $preselectedStudentId = (int)($_GET['student_id'] ?? 0);
        $preselectedCourseId  = (int)($_GET['course_id'] ?? 0);

        $preselectedStudent = null;
        if ($preselectedStudentId) {
            $preselectedStudent = $this->db->fetchOne(
                "SELECT id, name, email FROM users WHERE id = ? AND role = 'student'",
                [$preselectedStudentId]
            );
        }

        $preselectedCourse = null;
        if ($preselectedCourseId) {
            $preselectedCourse = $this->db->fetchOne(
                "SELECT c.id, c.title, c.course_code, cat.name AS category_name
                 FROM lms_courses c LEFT JOIN lms_categories cat ON cat.id = c.category_id
                 WHERE c.id = ?",
                [$preselectedCourseId]
            );
        }

        $view     = new View();
        $students = $this->db->fetchAll("SELECT id, name, email FROM users WHERE role = 'student' ORDER BY name ASC");
        $courses  = $this->db->fetchAll(
            "SELECT c.id, c.title, c.course_code, c.status, cat.name AS category_name
             FROM lms_courses c
             LEFT JOIN lms_categories cat ON cat.id = c.category_id
             ORDER BY c.title ASC"
        );

        // Already enrolled courses for this student (to disable them in course select)
        $enrolled = [];
        if ($preselectedStudentId) {
            $rows = $this->db->fetchAll(
                "SELECT course_id FROM lms_enrollments WHERE student_id = ?",
                [$preselectedStudentId]
            );
            $enrolled = array_column($rows, 'course_id');
        }

        // Students already enrolled in the preselected course (to mark in student select)
        $enrolledStudentIds = [];
        if ($preselectedCourseId) {
            $rows = $this->db->fetchAll(
                "SELECT student_id FROM lms_enrollments WHERE course_id = ?",
                [$preselectedCourseId]
            );
            $enrolledStudentIds = array_column($rows, 'student_id');
        }

        $view->render('admin/views/lms/students/create', [
            'title'              => 'Inscribir Estudiante',
            'students'           => $students,
            'courses'            => $courses,
            'preselectedStudent' => $preselectedStudent,
            'preselectedCourse'  => $preselectedCourse,
            'enrolled'           => $enrolled,
            'enrolledStudentIds' => $enrolledStudentIds,
        ], 'admin/views/layout');
    }

    /**
     * Store a manual enrollment.
     */
    public function store()
    {
        $this->requireAuth();
        $this->validateCsrf('/manager/lms/students');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/students');
        }

        $studentId = (int)($_POST['student_id'] ?? 0);
        $courseId  = (int)($_POST['course_id'] ?? 0);
        $backUrl   = $_POST['_back_url'] ?? '';
        // Only allow internal back URLs (no open redirect)
        $safeBack  = (strpos($backUrl, '/manager/') === 0) ? $backUrl : '';

        if ($this->enrollmentModel->isEnrolled($studentId, $courseId)) {
            $this->flash('error', 'Este estudiante ya está inscrito en ese curso.');
            // Return to the create form so the error is visible
            $this->redirect($safeBack ?: '/manager/lms/students/create');
            return;
        }

        $this->enrollmentModel->enroll($studentId, $courseId);
        $this->flash('success', 'Estudiante inscrito correctamente.');

        // If we came from a specific course's student list, go back there
        if ($courseId && strpos($safeBack, 'course_id=') !== false) {
            $this->redirect("/manager/lms/courses/{$courseId}/students");
            return;
        }

        $this->redirect('/manager/lms/students');
    }

    /**
     * Unenroll a student.
     */
    public function delete($id)
    {
        $this->requireAuth();
        $this->validateCsrf('/manager/lms/students');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/students');
        }

        $this->db->query("DELETE FROM lms_enrollments WHERE id = ?", [$id]);
        
        $this->flash('success', 'Inscripción eliminada.');
        $this->redirect('/manager/lms/students');
    }
}
