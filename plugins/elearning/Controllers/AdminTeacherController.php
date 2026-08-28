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

        $view = new View();

        $teachers = $this->db->fetchAll(
            "SELECT u.*,
                    (SELECT COUNT(*) FROM lms_courses WHERE teacher_id = u.id) as course_count
             FROM users u
             WHERE u.role = 'teacher'
             ORDER BY u.created_at DESC"
        );

        $stats = [
            'total'   => count($teachers),
            'active'  => count(array_filter($teachers, fn($t) => $t['status'] === 'active')),
            'blocked' => count(array_filter($teachers, fn($t) => $t['status'] === 'inactive')),
        ];

        $view->render('admin/views/lms/teachers/index', [
            'title'    => 'Gestión de Instructores',
            'teachers' => $teachers,
            'stats'    => $stats,
        ], 'admin/views/layout');
    }
}
