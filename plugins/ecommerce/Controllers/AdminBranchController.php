<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Core\Auth;
use Plugins\Ecommerce\Models\Branch;

class AdminBranchController extends Controller
{
    private $branchModel;

    public function __construct()
    {
        parent::__construct();
        $this->branchModel = new Branch();
    }

    public function index()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $branches = $this->branchModel->all();

        foreach ($branches as &$branch) {
            $count = $this->branchModel->getProductCount($branch['id']);
            $branch['product_count'] = $count['count'] ?? 0;
        }

        $this->view->render('plugins/ecommerce/views/admin/branches', [
            'title' => 'Branches',
            'branches' => $branches
        ], 'admin/views/layout');
    }

    public function create()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $this->view->render('plugins/ecommerce/views/admin/branch-form', [
            'title' => 'Create Branch',
            'branch' => null
        ], 'admin/views/layout');
    }

    public function store()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slug = !empty($_POST['slug']) ? $_POST['slug'] : strtolower(str_replace(' ', '-', $_POST['name']));

            $this->branchModel->create([
                'name' => $_POST['name'],
                'slug' => $slug,
                'description' => $_POST['description'] ?? '',
                'icon' => $_POST['icon'] ?? 'fa-anchor',
                'display_order' => $_POST['display_order'] ?? 0
            ]);

            $this->redirect('/manager/branches');
        }
    }

    public function edit($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $branch = $this->branchModel->find($id);

        $this->view->render('plugins/ecommerce/views/admin/branch-form', [
            'title' => 'Edit Branch',
            'branch' => $branch
        ], 'admin/views/layout');
    }

    public function update($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slug = isset($_POST['slug']) ? $_POST['slug'] : strtolower(str_replace(' ', '-', $_POST['name']));

            $this->branchModel->update($id, [
                'name' => $_POST['name'],
                'slug' => $slug,
                'description' => $_POST['description'] ?? '',
                'icon' => $_POST['icon'] ?? 'fa-anchor',
                'display_order' => $_POST['display_order'] ?? 0
            ]);

            $this->redirect('/manager/branches');
        }
    }

    public function delete($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->branchModel->hasProducts($id)) {
                $_SESSION['error'] = 'Cannot delete branch. It still has products assigned. Please reassign them first.';
                $this->redirect('/manager/branches');
                return;
            }

            $this->branchModel->delete($id);
            $_SESSION['success'] = 'Branch deleted successfully.';
        }

        $this->redirect('/manager/branches');
    }
}
