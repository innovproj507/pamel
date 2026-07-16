<?php

declare(strict_types=1);

namespace Plugins\Elearning\Controllers;

use Core\Controller;
use Core\ApiToken;
use Plugins\Elearning\Models\Forum;

class ForumsApiController extends Controller
{
    private Forum $forumModel;

    public function __construct()
    {
        parent::__construct();
        $this->forumModel = new Forum();
    }

    /** GET /api/v1/forums */
    public function index(): void
    {
        $forums = $this->forumModel->allActiveWithCount();
        $this->apiResponse(['status' => 'success', 'data' => $forums]);
    }

    /** GET /api/v1/forums/:slug */
    public function show($slug): void
    {
        $forum = $this->forumModel->findBySlug($slug);
        if (!$forum) {
            $this->apiResponse(['status' => 'error', 'message' => 'Foro no encontrado'], 404);
        }
        $topics = $this->forumModel->topicsByForum((int) $forum['id']);
        $this->apiResponse(['status' => 'success', 'data' => ['forum' => $forum, 'topics' => $topics]]);
    }

    /** GET /api/v1/topics/:id */
    public function showTopic($id): void
    {
        $topic = $this->forumModel->findTopic((int) $id);
        if (!$topic) {
            $this->apiResponse(['status' => 'error', 'message' => 'Tema no encontrado'], 404);
        }
        $forumId = (int) ($topic['forum_id'] ?? 0);
        $forum   = ($forumId > 0) ? ($this->forumModel->find($forumId) ?: []) : [];
        $posts   = $this->forumModel->postsByTopic((int) $id);
        $this->apiResponse(['status' => 'success', 'data' => ['topic' => $topic, 'forum' => $forum, 'posts' => $posts]]);
    }

    /** POST /api/v1/topics */
    public function storeTopic(): void
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $forumId = (int) ($body['forum_id'] ?? 0);
        $title   = trim($body['title'] ?? '');
        $content = trim($body['content'] ?? '');

        if (!$forumId || empty($title) || empty($content)) {
            $this->apiResponse(['status' => 'error', 'message' => 'Faltan datos requeridos'], 400);
        }

        $forum = $this->forumModel->find($forumId);
        if (!$forum || !$forum['is_active']) {
            $this->apiResponse(['status' => 'error', 'message' => 'Foro no encontrado'], 404);
        }

        $user = $this->db->fetchOne('SELECT name FROM users WHERE id = ?', [$userId]);
        $userName = $user['name'] ?? 'Usuario';

        $topicId = $this->forumModel->createTopic([
            'forum_id'   => $forumId,
            'user_id'    => $userId,
            'user_name'  => $userName,
            'title'      => $title,
            'content'    => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->apiResponse(['status' => 'success', 'data' => ['id' => $topicId]]);
    }

    /** POST /api/v1/topics/:id/reply */
    public function storePost($id): void
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $topic = $this->forumModel->findTopic((int) $id);
        if (!$topic) {
            $this->apiResponse(['status' => 'error', 'message' => 'Tema no encontrado'], 404);
        }
        if (!empty($topic['is_locked'])) {
            $this->apiResponse(['status' => 'error', 'message' => 'Este tema está cerrado'], 403);
        }

        $body    = json_decode(file_get_contents('php://input'), true) ?? [];
        $content = trim($body['content'] ?? '');
        if (empty($content)) {
            $this->apiResponse(['status' => 'error', 'message' => 'El contenido es requerido'], 400);
        }

        $user = $this->db->fetchOne('SELECT name FROM users WHERE id = ?', [$userId]);
        $userName = $user['name'] ?? 'Usuario';

        $postId = $this->forumModel->createPost([
            'topic_id'   => (int) $id,
            'user_id'    => $userId,
            'user_name'  => $userName,
            'content'    => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->apiResponse(['status' => 'success', 'data' => ['id' => $postId]]);
    }

    /** POST /api/v1/topics/:id/delete */
    public function deleteTopic($id): void
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $topic = $this->forumModel->findTopic((int) $id);
        if (!$topic) {
            $this->apiResponse(['status' => 'error', 'message' => 'Tema no encontrado'], 404);
        }

        // Only owner or admin can delete
        $user = $this->db->fetchOne('SELECT role FROM users WHERE id = ?', [$userId]);
        if ((int) $topic['user_id'] !== $userId && ($user['role'] ?? '') !== 'admin') {
            $this->apiResponse(['status' => 'error', 'message' => 'Sin permiso'], 403);
        }

        $this->forumModel->deleteTopic((int) $id);
        $this->apiResponse(['status' => 'success', 'message' => 'Tema eliminado']);
    }

    /** GET /api/v1/courses/:id/forum */
    public function courseTopics($courseId): void
    {
        $topics = $this->db->fetchAll(
            'SELECT t.*, COUNT(p.id) AS replies_count
             FROM lms_forum_topics t
             LEFT JOIN lms_forum_posts p ON p.topic_id = t.id
             WHERE t.course_id = ?
             GROUP BY t.id
             ORDER BY t.created_at DESC',
            [(int) $courseId]
        );
        $this->apiResponse(['status' => 'success', 'data' => $topics]);
    }

    /** POST /api/v1/courses/:id/forum */
    public function storeCourseTopics($courseId): void
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $body    = json_decode(file_get_contents('php://input'), true) ?? [];
        $title   = trim($body['title'] ?? '');
        $content = trim($body['content'] ?? '');

        if (empty($title) || empty($content)) {
            $this->apiResponse(['status' => 'error', 'message' => 'Faltan datos requeridos'], 400);
        }

        $user     = $this->db->fetchOne('SELECT name FROM users WHERE id = ?', [$userId]);
        $userName = $user['name'] ?? 'Usuario';

        $topicId = $this->forumModel->createTopic([
            'forum_id'   => 0,
            'course_id'  => (int) $courseId,
            'user_id'    => $userId,
            'user_name'  => $userName,
            'title'      => $title,
            'content'    => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->apiResponse(['status' => 'success', 'data' => ['id' => $topicId]]);
    }

    /** POST /api/v1/posts/:id/delete */
    public function deletePost($id): void
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $post = $this->db->fetchOne('SELECT * FROM lms_forum_posts WHERE id = ?', [(int) $id]);
        if (!$post) {
            $this->apiResponse(['status' => 'error', 'message' => 'Respuesta no encontrada'], 404);
        }

        $user = $this->db->fetchOne('SELECT role FROM users WHERE id = ?', [$userId]);
        if ((int) $post['user_id'] !== $userId && ($user['role'] ?? '') !== 'admin') {
            $this->apiResponse(['status' => 'error', 'message' => 'Sin permiso'], 403);
        }

        $this->forumModel->deletePost((int) $id);
        $this->apiResponse(['status' => 'success', 'message' => 'Respuesta eliminada']);
    }

    private function apiResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
