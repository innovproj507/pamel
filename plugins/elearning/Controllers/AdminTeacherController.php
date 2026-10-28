<?php

namespace Plugins\Elearning\Controllers;

use Core\View;

class AdminTeacherController extends BaseController
{
    /**
     * List all instructors with how many courses each has assigned.
     */
    public function index()
    {
        $this->requireCan('lms.teachers.view');

        // Filas por página: lista cerrada, porque el valor entra en el LIMIT.
        $perPage = (int)($_GET['per_page'] ?? 20);
        if (!in_array($perPage, [10, 20, 50, 100], true)) { $perPage = 20; }
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $q       = trim($_GET['q'] ?? '');
        $status  = $_GET['status'] ?? '';

        $where  = ["u.role = 'teacher'"];
        $params = [];

        if ($q !== '') {
            $where[]  = '(u.name LIKE ? OR u.email LIKE ?)';
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        }
        if (in_array($status, ['active', 'inactive'], true)) {
            $where[]  = 'u.status = ?';
            $params[] = $status;
        }
        $whereStr = implode(' AND ', $where);

        // Un único recuento para el paginador y los recuadros de resumen.
        $stats = $this->db->fetchOne(
            "SELECT COUNT(*) total,
                    SUM(u.status = 'active')   activos,
                    SUM(u.status = 'inactive') bloqueados,
                    COALESCE(SUM((SELECT COUNT(*) FROM lms_courses WHERE teacher_id = u.id)), 0) cursos
               FROM users u
              WHERE {$whereStr}",
            $params
        ) ?: [];

        $total  = (int)($stats['total'] ?? 0);
        $offset = ($page - 1) * $perPage;

        $teachers = $this->db->fetchAll(
            "SELECT u.*,
                    (SELECT COUNT(*) FROM lms_courses WHERE teacher_id = u.id) as course_count
             FROM users u
             WHERE {$whereStr}
             ORDER BY u.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $view = new View();
        $view->render('admin/views/lms/teachers/index', [
            'title'        => 'Gestión de Instructores',
            'teachers'     => $teachers,
            'stats'        => $stats,
            'total'        => $total,
            'page'         => $page,
            'perPage'      => $perPage,
            'lastPage'     => (int)ceil($total / $perPage),
            'q'            => $q,
            'filterStatus' => $status,
        ], 'admin/views/layout');
    }
}
