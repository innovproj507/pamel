<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Core\Database;
use Core\ApiToken;

class AuthApiController extends Controller
{
    /**
     * POST /api/v1/login
     */
    public function login()
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $email    = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->apiResponse(['status' => 'error', 'message' => 'Email y contraseña requeridos'], 400);
        }

        $db   = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->apiResponse(['status' => 'error', 'message' => 'Credenciales inválidas'], 401);
        }

        if (($user['status'] ?? '') !== 'active') {
            $this->apiResponse(['status' => 'error', 'message' => 'Cuenta inactiva'], 403);
        }

        $allowedRoles = ['student', 'teacher', 'admin'];
        if (!in_array($user['role'], $allowedRoles, true)) {
            $this->apiResponse(['status' => 'error', 'message' => 'No tienes permiso para acceder al LMS'], 403);
        }

        $token = ApiToken::generate((int) $user['id']);

        $this->apiResponse([
            'status' => 'success',
            'token'  => $token,
            'user'   => [
                'id'    => (int) $user['id'],
                'name'  => $user['name'],
                'email' => $user['email'],
                'role'  => $user['role'],
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/register
     */
    public function register()
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $name     = trim($data['name'] ?? '');
        $email    = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            $this->apiResponse(['status' => 'error', 'message' => 'Nombre, email y contraseña requeridos'], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->apiResponse(['status' => 'error', 'message' => 'Email inválido'], 400);
        }

        if (strlen($password) < 8) {
            $this->apiResponse(['status' => 'error', 'message' => 'La contraseña debe tener al menos 8 caracteres'], 400);
        }

        $db = Database::getInstance();

        if ($db->fetchOne("SELECT id FROM users WHERE email = ? LIMIT 1", [$email])) {
            $this->apiResponse(['status' => 'error', 'message' => 'El email ya está registrado'], 409);
        }

        $userId = $db->insert('users', [
            'name'     => $name,
            'email'    => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => 'student',
            'status'   => 'active',
        ]);

        if (!$userId) {
            $this->apiResponse(['status' => 'error', 'message' => 'Error al crear la cuenta'], 500);
        }

        $token = ApiToken::generate((int) $userId);

        $this->apiResponse([
            'status' => 'success',
            'token'  => $token,
            'user'   => [
                'id'    => (int) $userId,
                'name'  => $name,
                'email' => $email,
                'role'  => 'student',
            ],
        ], 201);
    }

    /**
     * GET /api/v1/auth/me  (requires Bearer token)
     */
    public function me()
    {
        $userId = ApiToken::fromRequest();
        if ($userId === false) {
            $this->apiResponse(['status' => 'error', 'message' => 'Token inválido o expirado'], 401);
        }

        $db   = Database::getInstance();
        $user = $db->fetchOne(
            "SELECT id, name, email, role, status, created_at FROM users WHERE id = ? LIMIT 1",
            [$userId]
        );

        if (!$user) {
            $this->apiResponse(['status' => 'error', 'message' => 'Usuario no encontrado'], 404);
        }

        $this->apiResponse(['status' => 'success', 'data' => $user]);
    }

    private function apiResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}
