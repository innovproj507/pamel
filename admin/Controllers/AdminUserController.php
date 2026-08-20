<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Models\User;
use Core\Security;

class AdminUserController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        require_once __DIR__ . '/../../config/security_init.php';
        $this->userModel = new User();
    }

    public function index()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $search  = trim($_GET['search'] ?? '');
        $role    = $_GET['role']   ?? '';
        $status  = $_GET['status'] ?? '';

        $filters = array_filter(['search' => $search, 'role' => $role, 'status' => $status]);

        $users      = $this->userModel->paginate($page, $perPage, $filters);
        $totalUsers = $this->userModel->count($filters);
        $totalPages = max(1, (int)ceil($totalUsers / $perPage));

        $this->view->render('admin/views/users/index', [
            'title'        => 'Users',
            'users'        => $users,
            'currentPage'  => $page,
            'totalPages'   => $totalPages,
            'totalUsers'   => $totalUsers,
            'search'       => $search,
            'roleFilter'   => $role,
            'statusFilter' => $status,
        ], 'admin/views/layout');
    }

    public function create()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $this->view->render('admin/views/users/create', [
            'title' => 'Create User'
        ], 'admin/views/layout');
    }

    public function store()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /manager/users');
            exit;
        }

        // Validar CSRF
        if (!isset($_POST['csrf_token']) || !Security::validateCsrfToken($_POST['csrf_token'])) {
            Security::logSecurityEvent('csrf_validation_failed', ['action' => 'create_user']);
            header('Location: /manager/users/create?error=' . urlencode('Token de seguridad inválido'));
            exit;
        }

        // Validar datos
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $role = $_POST['role'] ?? 'customer';
        $status = $_POST['status'] ?? 'active';

        // Validaciones
        if (empty($name) || empty($email) || empty($password)) {
            header('Location: /manager/users/create?error=' . urlencode('Todos los campos son requeridos'));
            exit;
        }

        if (!Security::validateEmail($email)) {
            header('Location: /manager/users/create?error=' . urlencode('Email inválido'));
            exit;
        }

        if (strlen($password) < 6) {
            header('Location: /manager/users/create?error=' . urlencode('La contraseña debe tener al menos 6 caracteres'));
            exit;
        }

        if ($password !== $passwordConfirm) {
            header('Location: /manager/users/create?error=' . urlencode('Las contraseñas no coinciden'));
            exit;
        }

        // Validar que el email no exista
        if ($this->userModel->findByEmail($email)) {
            header('Location: /manager/users/create?error=' . urlencode('El email ya está registrado'));
            exit;
        }

        // Validar rol
        if (!in_array($role, ['admin', 'student', 'teacher', 'editor'])) {
            $role = 'student';
        }

        // Validar estado
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        // Crear usuario
        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'status' => $status
        ];

        if ($this->userModel->create($data)) {
            Security::logSecurityEvent('user_created', [
                'email' => $email,
                'role' => $role,
                'created_by' => $_SESSION['user_id'] ?? 'unknown'
            ]);

            $listPath = $role === 'teacher' ? '/manager/lms/teachers' : '/manager/users';
            header('Location: ' . $listPath . '?success=' . urlencode('Usuario creado exitosamente'));
            exit;
        }

        header('Location: /manager/users/create?error=' . urlencode('Error al crear el usuario'));
        exit;
    }

    public function edit($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        // Validar ID
        $id = Security::validateId($id);
        if (!$id) {
            header('Location: /manager/users?error=' . urlencode('ID de usuario inválido'));
            exit;
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            header('Location: /manager/users?error=' . urlencode('Usuario no encontrado'));
            exit;
        }

        $this->view->render('admin/views/users/edit', [
            'title' => 'Edit User',
            'user' => $user
        ], 'admin/views/layout');
    }

    public function update($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /manager/users');
            exit;
        }

        // Validar CSRF
        if (!isset($_POST['csrf_token']) || !Security::validateCsrfToken($_POST['csrf_token'])) {
            Security::logSecurityEvent('csrf_validation_failed', ['action' => 'update_user']);
            header("Location: /manager/users/$id/edit?error=" . urlencode('Token de seguridad inválido'));
            exit;
        }

        // Validar ID
        $id = Security::validateId($id);
        if (!$id) {
            header('Location: /manager/users?error=' . urlencode('ID de usuario inválido'));
            exit;
        }

        // Validar datos
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'role' => $_POST['role'] ?? 'customer',
            'status' => $_POST['status'] ?? 'active'
        ];

        // Validaciones
        if (empty($data['name']) || empty($data['email'])) {
            header("Location: /manager/users/$id/edit?error=" . urlencode('Nombre y email son requeridos'));
            exit;
        }

        if (!Security::validateEmail($data['email'])) {
            header("Location: /manager/users/$id/edit?error=" . urlencode('Email inválido'));
            exit;
        }

        // Validar rol
        if (!in_array($data['role'], ['admin', 'student', 'teacher', 'editor'])) {
            $data['role'] = 'student';
        }

        // Validar estado
        if (!in_array($data['status'], ['active', 'inactive'])) {
            $data['status'] = 'active';
        }

        // Verificar email duplicado antes de actualizar
        $existing = $this->userModel->findByEmail($data['email']);
        if ($existing && (int)$existing['id'] !== $id) {
            header("Location: /manager/users/$id/edit?error=" . urlencode('El email ya está en uso por otro usuario'));
            exit;
        }

        if ($this->userModel->updateProfile($id, $data)) {
            // Actualizar contraseña si se proporcionó
            if (!empty($_POST['password'])) {
                if (strlen($_POST['password']) < 6) {
                    header("Location: /manager/users/$id/edit?error=" . urlencode('La contraseña debe tener al menos 6 caracteres'));
                    exit;
                }
                $this->userModel->updatePassword($id, $_POST['password']);
            }

            Security::logSecurityEvent('user_updated', [
                'user_id' => $id,
                'updated_by' => $_SESSION['user_id'] ?? 'unknown'
            ]);

            header('Location: /manager/users?success=' . urlencode('Usuario actualizado exitosamente'));
            exit;
        }

        header("Location: /manager/users/$id/edit?error=" . urlencode('Error al actualizar el usuario'));
        exit;
    }

    public function delete($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        // Validar ID
        $id = Security::validateId($id);
        if (!$id) {
            header('Location: /manager/users?error=' . urlencode('ID de usuario inválido'));
            exit;
        }

        // No permitir eliminar el propio usuario
        if ($id == ($_SESSION['user_id'] ?? 0)) {
            header('Location: /manager/users?error=' . urlencode('No puedes eliminar tu propio usuario'));
            exit;
        }

        if ($this->userModel->delete($id)) {
            Security::logSecurityEvent('user_deleted', [
                'user_id' => $id,
                'deleted_by' => $_SESSION['user_id'] ?? 'unknown'
            ]);
            
            header('Location: /manager/users?success=' . urlencode('Usuario eliminado exitosamente'));
            exit;
        }

        header('Location: /manager/users?error=' . urlencode('Error al eliminar el usuario'));
        exit;
    }

    public function toggleStatus($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        // Validar ID
        $id = Security::validateId($id);
        if (!$id) {
            header('Location: /manager/users?error=' . urlencode('ID de usuario inválido'));
            exit;
        }

        // No permitir desactivar el propio usuario
        if ($id == ($_SESSION['user_id'] ?? 0)) {
            header('Location: /manager/users?error=' . urlencode('No puedes desactivar tu propio usuario'));
            exit;
        }

        if ($this->userModel->toggleStatus($id)) {
            Security::logSecurityEvent('user_status_toggled', [
                'user_id' => $id,
                'toggled_by' => $_SESSION['user_id'] ?? 'unknown'
            ]);
            
            header('Location: /manager/users?success=' . urlencode('Estado actualizado exitosamente'));
            exit;
        }

        header('Location: /manager/users?error=' . urlencode('Error al actualizar el estado'));
        exit;
    }
}
