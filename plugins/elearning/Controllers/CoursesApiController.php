<?php

declare(strict_types=1);

namespace Plugins\Elearning\Controllers;

use Core\Controller;
use Core\ApiToken;
use Plugins\Elearning\Models\Course;
use Plugins\Elearning\Models\Enrollment;

class CoursesApiController extends Controller
{
    private Course $courseModel;
    private Enrollment $enrollmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->courseModel     = new Course();
        $this->enrollmentModel = new Enrollment();
    }

    /**
     * GET /api/v1/courses
     */
    public function index()
    {
        $page       = max(1, (int) ($_GET['page'] ?? 1));
        $perPage    = min(50, max(1, (int) ($_GET['perPage'] ?? 12)));
        $categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
        $status     = $_GET['status'] ?? 'published';

        if ($status !== 'published') {
            $status = 'published';
        }

        $result = $this->courseModel->published($page, $perPage, $categoryId);

        $this->apiResponse(['status' => 'success', 'data' => $result]);
    }

    /**
     * GET /api/v1/courses/{id}
     */
    public function show($id)
    {
        $course = $this->courseModel->find((int) $id);
        if (!$course) {
            $this->apiResponse(['status' => 'error', 'message' => 'Curso no encontrado'], 404);
        }
        $this->apiResponse(['status' => 'success', 'data' => $course]);
    }

    /**
     * GET /api/v1/courses/slug/{slug}
     */
    public function bySlug($slug)
    {
        $course = $this->courseModel->findBySlug($slug);
        if (!$course) {
            $this->apiResponse(['status' => 'error', 'message' => 'Curso no encontrado'], 404);
        }
        $this->apiResponse(['status' => 'success', 'data' => $course]);
    }

    /**
     * GET /api/v1/courses/{id}/content
     * Returns lessons and quizzes for a course.
     */
    public function content($id)
    {
        $course = $this->courseModel->find((int) $id);
        if (!$course) {
            $this->apiResponse(['status' => 'error', 'message' => 'Curso no encontrado'], 404);
        }

        $content = $this->courseModel->getLessonsAndQuizzes((int) $id);
        $this->apiResponse(['status' => 'success', 'data' => $content]);
    }

    /**
     * GET /api/v1/courses/{id}/check-enrollment?user_id=X
     */
    public function checkEnrollment($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : null;
        }

        if (!$userId) {
            $this->apiResponse(['status' => 'success', 'enrolled' => false]);
        }

        $enrolled = $this->enrollmentModel->isEnrolled((int) $userId, (int) $id);
        $this->apiResponse(['status' => 'success', 'enrolled' => $enrolled]);
    }

    /**
     * GET /api/v1/courses/count
     */
    public function count()
    {
        $total = $this->courseModel->countPublished();
        $this->apiResponse(['status' => 'success', 'count' => $total]);
    }

    /**
     * POST /api/v1/courses
     */
    public function store()
    {
        if (ApiToken::fromRequest() === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        if (empty($body['title'])) {
            $this->apiResponse(['status' => 'error', 'message' => 'Título requerido'], 400);
        }

        $slug = $this->toSlug($body['title']);
        $data = [
            'teacher_id'  => (int) ($body['teacher_id'] ?? 0),
            'category_id' => !empty($body['category_id']) ? (int) $body['category_id'] : null,
            'title'       => $body['title'],
            'slug'        => $slug,
            'description' => $body['description'] ?? '',
            'price'       => (float) ($body['price'] ?? 0),
            'is_free'     => (int) ($body['is_free'] ?? 0),
            'level'       => $body['level'] ?? 'beginner',
            'status'      => $body['status'] ?? 'draft',
            'image'       => $body['image'] ?? null,
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $id = $this->courseModel->create($data);
        if (!$id) {
            $this->apiResponse(['status' => 'error', 'message' => 'Error al crear el curso'], 500);
        }

        $this->apiResponse(['status' => 'success', 'data' => ['id' => $id, 'slug' => $slug]]);
    }

    /**
     * POST /api/v1/courses/{id}/update
     */
    public function update($id)
    {
        if (ApiToken::fromRequest() === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $course = $this->courseModel->find((int) $id);
        if (!$course) {
            $this->apiResponse(['status' => 'error', 'message' => 'Curso no encontrado'], 404);
        }

        $body     = json_decode(file_get_contents('php://input'), true) ?? [];
        $fillable = ['title', 'category_id', 'description', 'price', 'is_free', 'level', 'status', 'image', 'satisfaction_enabled'];
        $data     = ['updated_at' => date('Y-m-d H:i:s')];
        foreach ($fillable as $field) {
            if (array_key_exists($field, $body)) {
                $data[$field] = $body[$field];
            }
        }

        $this->courseModel->update((int) $id, $data);
        $this->apiResponse(['status' => 'success', 'message' => 'Curso actualizado']);
    }

    /**
     * POST /api/v1/courses/{id}/delete
     */
    public function destroy($id)
    {
        if (ApiToken::fromRequest() === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $course = $this->courseModel->find((int) $id);
        if (!$course) {
            $this->apiResponse(['status' => 'error', 'message' => 'Curso no encontrado'], 404);
        }

        $this->courseModel->delete((int) $id);
        $this->apiResponse(['status' => 'success', 'message' => 'Curso eliminado']);
    }

    private function toSlug(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        return preg_replace('/[\s-]+/', '-', trim($text));
    }

    private function apiResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
