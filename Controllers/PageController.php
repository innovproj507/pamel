<?php

namespace Controllers;

use Core\View;
use Core\Database;

class PageController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function show($slug)
    {
        // Fetch page from database by slug
        $page = $this->db->fetchOne(
            "SELECT * FROM pages WHERE slug = :slug AND status = 'published'",
            ['slug' => $slug]
        );

        if (!$page) {
            http_response_code(404);
            echo "404 - Page Not Found";
            return;
        }

        // Render the page
        $view = new View();
        $view->render('public/views/page', [
            'title' => $page['title'],
            'page' => $page
        ], 'public/views/layout');
    }
}
