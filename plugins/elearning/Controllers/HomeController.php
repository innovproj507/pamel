<?php

namespace Plugins\Elearning\Controllers;

use Core\View;
use Core\Database;
use Core\Config;

class HomeController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * LMS Home Page
     */
    public function index()
    {
        $view = new View();
        
        // Fetch featured courses
        $featured = $this->db->fetchAll(
            "SELECT * FROM lms_courses WHERE status = 'published' ORDER BY created_at DESC LIMIT 8"
        );
        
        // Fetch categories with course count
        $categories = $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM lms_courses WHERE category_id = c.id AND status = 'published') as course_count 
             FROM lms_categories c ORDER BY name ASC"
        );
        
        // Stats
        $heroStats = [
            'courses' => $this->db->fetchOne("SELECT COUNT(*) as count FROM lms_courses WHERE status = 'published'")['count'],
            'students' => $this->db->fetchOne("SELECT COUNT(*) as count FROM users WHERE role = 'student' AND status = 'active'")['count'],
            'categories' => count($categories)
        ];

        $view->render('plugins/elearning/Views/home', [
            'title' => 'E-learning | PAMEL',
            'featured' => $featured,
            'categories' => $categories,
            'heroStats' => $heroStats
        ], 'plugins/elearning/Views/layout');
    }
}
