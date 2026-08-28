<?php

namespace Plugins\Elearning\Controllers;

use Core\View;

class AdminCategoryController extends BaseController
{
    public function index()
    {
        $this->requireCan('lms.categories.manage');
        
        $view = new View();
        $categories = $this->db->fetchAll(
            "SELECT c.*, (SELECT COUNT(*) FROM lms_courses WHERE category_id = c.id) as course_count 
             FROM lms_categories c ORDER BY name ASC"
        );

        $view->render('admin/views/lms/categories/index', [
            'title'      => 'Categorías LMS',
            'categories' => $categories,
        ], 'admin/views/layout');
    }

    public function store()
    {
        $this->requireCan('lms.categories.manage');
        $this->validateCsrf('/manager/lms/categories');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/categories');
        }

        $name = $_POST['name'] ?? '';
        $slug = $this->generateSlug($name);

        $this->db->insert('lms_categories', [
            'name' => $name,
            'slug' => $slug,
            'description' => $_POST['description'] ?? ''
        ]);

        $this->flash('success', 'Categoría creada.');
        $this->redirect('/manager/lms/categories');
    }

    public function delete($id)
    {
        $this->requireCan('lms.categories.manage');
        $this->validateCsrf('/manager/lms/categories');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/lms/categories');
        }

        $this->db->query("DELETE FROM lms_categories WHERE id = ?", [$id]);
        
        $this->flash('success', 'Categoría eliminada.');
        $this->redirect('/manager/lms/categories');
    }

    private function generateSlug($string)
    {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return rtrim($string, '-');
    }
}
