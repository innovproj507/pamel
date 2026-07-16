<?php

declare(strict_types=1);

namespace Plugins\Elearning\Controllers;

use Core\Controller;
use Core\ApiToken;
use Plugins\Elearning\Models\Lesson;

class LessonsApiController extends Controller
{
    private Lesson $lessonModel;

    public function __construct()
    {
        parent::__construct();
        $this->lessonModel = new Lesson();
    }

    /**
     * GET /api/v1/lessons/{id}
     * Returns lesson data with completion status for the authenticated user.
     */
    public function show($id)
    {
        $lesson = $this->lessonModel->find((int) $id);
        if (!$lesson) {
            $this->apiResponse(['status' => 'error', 'message' => 'Lección no encontrada'], 404);
        }

        $userId    = ApiToken::fromRequest();
        $completed = $userId ? $this->lessonModel->isCompleted($userId, (int) $id) : false;

        $lesson['completed'] = $completed;
        $this->apiResponse(['status' => 'success', 'data' => $lesson]);
    }

    /**
     * POST /api/v1/lessons/{id}/complete
     * Marks a lesson as completed for the authenticated user.
     */
    public function complete($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $lesson = $this->lessonModel->find((int) $id);
        if (!$lesson) {
            $this->apiResponse(['status' => 'error', 'message' => 'Lección no encontrada'], 404);
        }

        $this->lessonModel->markCompleted($userId, (int) $id);
        $this->apiResponse(['status' => 'success', 'message' => 'Lección marcada como completada']);
    }

    /**
     * GET /api/v1/courses/{courseId}/lessons
     * Returns all active lessons for a course ordered by order_num.
     */
    public function byCourse($courseId)
    {
        $lessons = $this->lessonModel->byCourse((int) $courseId);
        $this->apiResponse(['status' => 'success', 'data' => $lessons ?: []]);
    }

    /**
     * GET /api/v1/courses/{courseId}/lessons/previous?order_num=X
     * Returns the previous active lesson before the given order_num.
     */
    public function previous($courseId)
    {
        $orderNum = (int) ($_GET['order_num'] ?? 0);
        $prev     = $this->lessonModel->previousInCourse((int) $courseId, $orderNum);
        $this->apiResponse(['status' => 'success', 'data' => $prev ?: null]);
    }

    /**
     * POST /api/v1/lessons/{id}/toggle
     * Body: { active: true|false }
     * Sets is_active state of a lesson. Requires auth.
     */
    public function toggle($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $lesson = $this->lessonModel->find((int) $id);
        if (!$lesson) {
            $this->apiResponse(['status' => 'error', 'message' => 'Lección no encontrada'], 404);
        }

        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $active = isset($body['active']) ? (bool) $body['active'] : !$lesson['is_active'];

        $this->lessonModel->setActive((int) $id, $active);
        $this->apiResponse(['status' => 'success', 'is_active' => $active]);
    }

    /**
     * POST /api/v1/lessons/{id}/delete
     * Deletes a lesson. Requires auth.
     */
    public function destroy($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $lesson = $this->lessonModel->find((int) $id);
        if (!$lesson) {
            $this->apiResponse(['status' => 'error', 'message' => 'Lección no encontrada'], 404);
        }

        $this->lessonModel->deleteLesson((int) $id);
        $this->apiResponse(['status' => 'success', 'message' => 'Lección eliminada']);
    }

    /**
     * POST /api/v1/lessons
     * Creates a new lesson. Requires auth.
     */
    public function store()
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        if (empty($body['course_id']) || empty($body['title'])) {
            $this->apiResponse(['status' => 'error', 'message' => 'Faltan datos requeridos'], 400);
        }

        $data = [
            'course_id' => (int) $body['course_id'],
            'title'     => $body['title'] ?? '',
            'content'   => $body['content'] ?? '',
            'video_url' => $body['video_url'] ?? '',
            'file_path' => $body['file_path'] ?? '',
            'type'      => $body['type'] ?? 'text',
            'order_num' => (int) ($body['order_num'] ?? 0),
            'duration'  => (int) ($body['duration'] ?? 0),
            'is_active' => 1,
            'created_at'=> date('Y-m-d H:i:s'),
        ];

        $this->lessonModel->create($data);
        $this->apiResponse(['status' => 'success', 'message' => 'Lección creada']);
    }

    /**
     * POST /api/v1/lessons/{id}/update
     * Updates an existing lesson. Requires auth.
     */
    public function update($id)
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $lesson = $this->lessonModel->find((int) $id);
        if (!$lesson) {
            $this->apiResponse(['status' => 'error', 'message' => 'Lección no encontrada'], 404);
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        error_log("LessonsApiController::update body: " . print_r($body, true));
        $data = [];
        $fillable = ['title', 'content', 'video_url', 'file_path', 'type', 'order_num', 'duration'];
        foreach ($fillable as $field) {
            if (isset($body[$field])) {
                $data[$field] = $body[$field];
            }
        }
        error_log("LessonsApiController::update data keys: " . implode(',', array_keys($data)));

        $res = $this->lessonModel->update((int) $id, $data);
        if (!$res) {
            error_log("LessonsApiController::update FAILED for lesson {$id}. Data keys: " . implode(',', array_keys($data)));
            $this->apiResponse(['status' => 'error', 'message' => 'Error actualizando en base de datos'], 500);
        }
        $this->apiResponse(['status' => 'success', 'message' => 'Lección actualizada']);
    }

    private function apiResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
