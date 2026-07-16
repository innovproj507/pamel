<?php

declare(strict_types=1);

namespace Controllers;

use App\Core\Controller;
use App\Core\Database;

class StatsApiController extends Controller
{
    /**
     * GET /api/courses/count
     */
    public function coursesCount()
    {
        $db = Database::getInstance();
        $count = $db->queryOne("SELECT COUNT(*) as total FROM lms_courses WHERE status = 'published'");
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'count' => $count['total'] ?? 0]);
        exit;
    }

    /**
     * GET /api/users/count
     */
    public function usersCount()
    {
        $db = Database::getInstance();
        $role = $_GET['role'] ?? 'student';
        $count = $db->queryOne("SELECT COUNT(*) as total FROM users WHERE role = ? AND is_active = 1", [$role]);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'count' => $count['total'] ?? 0]);
        exit;
    }
}
