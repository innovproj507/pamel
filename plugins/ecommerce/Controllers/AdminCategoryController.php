<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Core\Auth;
use Plugins\Ecommerce\Models\Category;

class AdminCategoryController extends Controller
{
    private $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $categories = $this->categoryModel->getAllWithHierarchy();
        
        // Get product counts and subcategory counts for each category
        foreach ($categories as &$category) {
            $count = $this->categoryModel->getProductCount($category['id']);
            $category['product_count'] = $count['count'] ?? 0;
            $category['has_subcategories'] = $this->categoryModel->hasSubcategories($category['id']);
        }

        $this->view->render('plugins/ecommerce/views/admin/categories', [
            'title' => 'Categories',
            'categories' => $categories
        ], 'admin/views/layout');
    }

    public function create()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $parentCategories = $this->categoryModel->getParentCategories();

        $this->view->render('plugins/ecommerce/views/admin/category-form', [
            'title' => 'Create Category',
            'category' => null,
            'parentCategories' => $parentCategories
        ], 'admin/views/layout');
    }

    public function store()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slug = strtolower(str_replace(' ', '-', $_POST['name']));

            $parentId = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

            $this->categoryModel->create([
                'parent_id' => $parentId,
                'name' => $_POST['name'],
                'slug' => $slug,
                'description' => $_POST['description'] ?? '',
                'icon' => $_POST['icon'] ?? 'fa-folder',
                'display_order' => $_POST['display_order'] ?? 0
            ]);

            $this->redirect('/manager/categories');
        }
    }

    public function edit($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $category = $this->categoryModel->find($id);
        $parentCategories = $this->categoryModel->getParentCategories();

        $this->view->render('plugins/ecommerce/views/admin/category-form', [
            'title' => 'Edit Category',
            'category' => $category,
            'parentCategories' => $parentCategories
        ], 'admin/views/layout');
    }

    public function update($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slug = isset($_POST['slug']) ? $_POST['slug'] : strtolower(str_replace(' ', '-', $_POST['name']));

            $parentId = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

            $this->categoryModel->update($id, [
                'parent_id' => $parentId,
                'name' => $_POST['name'],
                'slug' => $slug,
                'description' => $_POST['description'] ?? '',
                'icon' => $_POST['icon'] ?? 'fa-folder',
                'display_order' => $_POST['display_order'] ?? 0
            ]);

            $this->redirect('/manager/categories');
        }
    }

    public function delete($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if category has subcategories
            if ($this->categoryModel->hasSubcategories($id)) {
                $_SESSION['error'] = 'Cannot delete category. It has subcategories. Please delete or reassign subcategories first.';
                $this->redirect('/manager/categories');
                return;
            }

            $this->categoryModel->delete($id);
            $_SESSION['success'] = 'Category deleted successfully.';
        }

        $this->redirect('/manager/categories');
    }
}
