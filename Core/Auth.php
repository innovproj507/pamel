<?php

namespace Core;

class Auth
{
    private static $instance = null;
    private $user = null;

    private function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            $this->loadUser($_SESSION['user_id']);
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function login($email, $password)
    {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);

        if (!$user) {
            return false;
        }

        if ($user['status'] !== 'active') {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_logged_in'] = true;
            $this->user = $user;

            $db->update('users',
                ['last_login' => date('Y-m-d H:i:s')],
                'id = :id',
                ['id' => $user['id']]
            );

            return true;
        }

        return false;
    }

    public function logout()
    {
        $this->user = null;
        session_unset();
        session_destroy();
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
    }

    public function check()
    {
        return $this->user !== null;
    }

    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        return $this->user ? $this->user['id'] : null;
    }

    public function hasRole($role)
    {
        return $this->user && $this->user['role'] === $role;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    private function loadUser($userId)
    {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM users WHERE id = ? AND status = 'active'", [$userId]);
        $this->user = $user ?: null;
    }

    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function requireAuth($redirect = '/manager/login')
    {
        if (!$this->check()) {
            header('Location: ' . $redirect);
            exit;
        }
    }

    public function requireAdmin($redirect = '/manager/login')
    {
        if (!$this->isAdmin()) {
            header('Location: ' . $redirect);
            exit;
        }
    }

    /**
     * ¿El usuario actual tiene la capacidad indicada?
     */
    public function can($capability)
    {
        return $this->user && Permissions::roleCan($this->user['role'] ?? null, $capability);
    }

    /**
     * Exige una capacidad. Envía al login si no hay sesión, 403 si la hay
     * pero sin permiso (así no se confunde "no autenticado" con "no autorizado").
     */
    public function requireCan($capability, $redirect = '/manager/login')
    {
        $this->requireAuth($redirect);

        if (!$this->can($capability)) {
            http_response_code(403);
            echo 'Acceso denegado.';
            exit;
        }
    }
}
