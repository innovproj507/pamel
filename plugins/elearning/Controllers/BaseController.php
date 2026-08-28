<?php

namespace Plugins\Elearning\Controllers;

use Core\Database;
use Core\Config;
use Core\Security;

class BaseController
{
    protected $db;
    protected $user;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->user = $this->getAuthUser();
    }

    /**
     * Get the currently authenticated user from session
     */
    protected function getAuthUser()
    {
        if (isset($_SESSION['user_id'])) {
            return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
        }
        return null;
    }

    /**
     * Require authentication
     */
    protected function requireAuth()
    {
        if (!$this->user) {
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            $redirect = (strpos($uri, '/manager') === 0) ? '/manager/login' : '/login';
            header('Location: ' . $redirect);
            exit;
        }
    }

    /**
     * Require a specific role
     */
    protected function requireRole($roles)
    {
        $this->requireAuth();
        
        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (!in_array($this->user['role'], $roles)) {
            http_response_code(403);
            echo "Access Denied";
            exit;
        }
    }

    /**
     * Exige una capacidad del mapa central de permisos (Core\Permissions).
     */
    protected function requireCan(string $capability): void
    {
        $this->requireAuth();

        if (!\Core\Permissions::roleCan($this->user['role'] ?? null, $capability)) {
            http_response_code(403);
            echo 'Acceso denegado.';
            exit;
        }
    }

    /**
     * ¿El usuario actual tiene la capacidad indicada? (para condicionar vistas)
     */
    protected function can(string $capability): bool
    {
        return \Core\Permissions::roleCan($this->user['role'] ?? null, $capability);
    }

    /**
     * Fetch a course and ensure the current user may manage it (admin, or the
     * teacher it's assigned to). Redirects to the course list otherwise.
     */
    protected function authorizeCourseOwnership($courseId)
    {
        $course = $this->db->fetchOne("SELECT * FROM lms_courses WHERE id = ?", [(int) $courseId]);
        if (!$course) {
            $this->redirect('/manager/lms/courses');
        }
        $this->assertOwnsCourse($course);
        return $course;
    }

    /**
     * Check ownership on an already-fetched course row (avoids a second query).
     */
    protected function assertOwnsCourse(array $course)
    {
        $seesAllCourses = in_array(
            $this->user['role'],
            [\Core\Permissions::ADMIN, \Core\Permissions::ACADEMIC_DIRECTOR],
            true
        );

        if (!$seesAllCourses && (int) ($course['teacher_id'] ?? 0) !== (int) $this->user['id']) {
            $this->flash('error', 'No tienes permiso para gestionar este curso.');
            $this->redirect('/manager/lms/courses');
        }
    }

    /**
     * Redirect
     */
    protected function redirect($path)
    {
        header('Location: ' . $path);
        exit;
    }

    /**
     * Flash messages (proxy to Session if available)
     */
    protected function flash($type, $message)
    {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Validate CSRF token; redirect with error on failure.
     */
    protected function validateCsrf(string $redirectUrl = '/'): void
    {
        if (!isset($_POST['csrf_token']) || !\Core\Security::validateCsrfToken($_POST['csrf_token'])) {
            $this->flash('error', 'Token de seguridad inválido. Por favor recarga la página e intenta de nuevo.');
            $this->redirect($redirectUrl);
        }
    }
}
