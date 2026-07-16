<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Database;
use Core\ApiToken;

class UsersApiController extends Controller
{
    /**
     * GET /api/v1/users/{id}
     */
    public function show($id)
    {
        $requesterId = ApiToken::fromRequest();
        if ($requesterId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Autenticación requerida'], 401);
        }

        $db   = Database::getInstance();
        $user = $db->fetchOne(
            "SELECT id, name, email, role, status, created_at FROM users WHERE id = ? LIMIT 1",
            [(int) $id]
        );

        if (!$user) {
            $this->apiResponse(['status' => 'error', 'message' => 'Usuario no encontrado'], 404);
        }

        $this->apiResponse(['status' => 'success', 'data' => $user]);
    }

    /**
     * GET /api/v1/users/count
     */
    public function count()
    {
        $db     = Database::getInstance();
        $result = $db->fetchOne("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
        $this->apiResponse(['status' => 'success', 'count' => (int) ($result['total'] ?? 0)]);
    }

    private function apiResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
