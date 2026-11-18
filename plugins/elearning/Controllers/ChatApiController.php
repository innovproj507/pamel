<?php

declare(strict_types=1);

namespace Plugins\Elearning\Controllers;

use Core\ApiToken;
use Core\Controller;
use Plugins\Elearning\Models\Chat;

/**
 * Chat interno alumno ↔ instructor/administración.
 *
 *   GET  /api/v1/chat/unread                       mensajes sin leer (contador del menú)
 *   GET  /api/v1/chat/conversations                conversaciones del usuario
 *   POST /api/v1/chat/conversations                abrir una (alumnado): {course_id?, subject, body}
 *   GET  /api/v1/chat/conversations/{id}?after=N   la conversación y sus mensajes (desde el id N)
 *   POST /api/v1/chat/conversations/{id}/messages  responder: {body}
 *
 * Las reglas de acceso viven en Plugins\Elearning\Models\Chat.
 */
class ChatApiController extends Controller
{
    private Chat $chat;

    public function __construct()
    {
        parent::__construct();
        $this->chat = new Chat();
    }

    public function unread(): void
    {
        $userId = $this->requireUser();
        $this->apiResponse(['status' => 'success', 'data' => ['count' => $this->chat->sinLeer($userId)]]);
    }

    public function index(): void
    {
        $userId = $this->requireUser();
        $this->apiResponse(['status' => 'success', 'data' => [
            'rol'            => $this->chat->rolDe($userId),
            'conversaciones' => $this->chat->conversaciones($userId),
        ]]);
    }

    public function store(): void
    {
        $userId = $this->requireUser();
        $d = $this->body();

        $r = $this->chat->abrir(
            $userId,
            !empty($d['course_id']) ? (int) $d['course_id'] : null,
            (string) ($d['subject'] ?? ''),
            (string) ($d['body'] ?? '')
        );
        if (isset($r['error'])) {
            $this->apiResponse(['status' => 'error', 'message' => $r['error']], 422);
        }
        $this->apiResponse(['status' => 'success', 'data' => [
            'id'            => $r['id'],
            'destinatarios' => $this->chat->destinatarios((int) $r['id'], $userId),
        ]], 201);
    }

    public function show($id): void
    {
        $userId = $this->requireUser();
        $conv = $this->chat->conversacion($userId, (int) $id);
        if (!$conv) {
            $this->apiResponse(['status' => 'error', 'message' => 'Conversación no encontrada.'], 404);
        }

        $mensajes = $this->chat->mensajes($userId, (int) $id, (int) ($_GET['after'] ?? 0)) ?? [];

        $this->apiResponse(['status' => 'success', 'data' => [
            'conversacion' => [
                'id'           => (int) $conv['id'],
                'subject'      => $conv['subject'],
                'course_id'    => $conv['course_id'] !== null ? (int) $conv['course_id'] : null,
                'course_title' => $conv['course_title'],
                'student_id'   => (int) $conv['student_id'],
                'student_name' => $conv['student_name'],
                'mi_rol'       => $conv['mi_rol'],
            ],
            'mensajes' => $mensajes,
        ]]);
    }

    public function reply($id): void
    {
        $userId = $this->requireUser();
        $d = $this->body();

        $r = $this->chat->responder($userId, (int) $id, (string) ($d['body'] ?? ''));
        if (isset($r['error'])) {
            $this->apiResponse(['status' => 'error', 'message' => $r['error']], (int) ($r['codigo'] ?? 422));
        }
        $this->apiResponse(['status' => 'success', 'data' => [
            'id'            => $r['id'],
            'destinatarios' => $this->chat->destinatarios((int) $id, $userId),
        ]], 201);
    }

    // ─── Internos ────────────────────────────────────────────────────

    private function requireUser(): int
    {
        $userId = ApiToken::fromRequest();
        if (!$userId) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }
        return (int) $userId;
    }

    private function body(): array
    {
        $d = json_decode((string) file_get_contents('php://input'), true);
        return is_array($d) ? $d : [];
    }

    private function apiResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
