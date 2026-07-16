<?php

declare(strict_types=1);

namespace Plugins\Elearning\Controllers;

use Core\Controller;
use Core\Database;

class CategoriesApiController extends Controller
{
    /**
     * GET /api/v1/categories
     */
    public function index()
    {
        $db = Database::getInstance();

        $categories = $db->fetchAll(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM lms_courses WHERE category_id = c.id AND status = 'published') as course_count
             FROM lms_categories c
             ORDER BY c.name ASC"
        );

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'data'   => $categories ?? [],
        ]);
        exit;
    }
}
