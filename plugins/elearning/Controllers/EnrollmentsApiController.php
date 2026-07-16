<?php

declare(strict_types=1);

namespace Plugins\Elearning\Controllers;

use Core\Controller;
use Core\ApiToken;
use Plugins\Elearning\Models\Enrollment;
use Plugins\Elearning\Models\Course;

class EnrollmentsApiController extends Controller
{
    private Enrollment $enrollmentModel;
    private Course $courseModel;

    public function __construct()
    {
        parent::__construct();
        $this->enrollmentModel = new Enrollment();
        $this->courseModel     = new Course();
    }

    /**
     * POST /api/v1/enrollments
     * Body: { course_id: int }
     */
    public function enroll()
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $data     = json_decode(file_get_contents('php://input'), true) ?? [];
        $courseId = (int) ($data['course_id'] ?? 0);

        if ($courseId <= 0) {
            $this->apiResponse(['status' => 'error', 'message' => 'course_id requerido'], 400);
        }

        $course = $this->courseModel->find($courseId);
        if (!$course) {
            $this->apiResponse(['status' => 'error', 'message' => 'Curso no encontrado'], 404);
        }

        if ($this->enrollmentModel->isEnrolled($userId, $courseId)) {
            $this->apiResponse(['status' => 'error', 'message' => 'Ya estás inscrito en este curso'], 409);
        }

        $id = $this->enrollmentModel->enroll($userId, $courseId);
        if (!$id) {
            $this->apiResponse(['status' => 'error', 'message' => 'Error al inscribirse'], 500);
        }

        $this->apiResponse(['status' => 'success', 'message' => 'Inscripción exitosa'], 201);
    }

    /**
     * GET /api/v1/enrollments/count
     */
    public function count()
    {
        $result = $this->db->fetchOne("SELECT COUNT(*) AS total FROM lms_enrollments");
        $this->apiResponse(['status' => 'success', 'count' => (int)($result['total'] ?? 0)]);
    }

    /**
     * GET /api/v1/student/{studentId}/courses
     */
    public function studentCourses($studentId)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $courses = $this->enrollmentModel->getByStudent((int) $studentId);
        $this->apiResponse(['status' => 'success', 'data' => $courses]);
    }

    /**
     * GET /api/v1/student/{studentId}/courses/{courseId}/progress
     * Returns array of completed lesson IDs for the student in that course.
     */
    public function studentCourseProgress($studentId, $courseId)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $lessonModel = new \Plugins\Elearning\Models\Lesson();
        $ids         = $lessonModel->completedByStudentInCourse((int) $studentId, (int) $courseId);
        $this->apiResponse(['status' => 'success', 'data' => $ids]);
    }

    private function apiResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
