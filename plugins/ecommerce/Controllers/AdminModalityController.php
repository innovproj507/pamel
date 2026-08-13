<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Core\Auth;
use Plugins\Ecommerce\Models\Modality;
use Plugins\Ecommerce\Models\Branch;

class AdminModalityController extends Controller
{
    private $modalityModel;

    public function __construct()
    {
        parent::__construct();
        $this->modalityModel = new Modality();
    }

    public function index()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $modalities = $this->modalityModel->all();
        $branches = (new Branch())->all();
        $branchesById = [];
        foreach ($branches as $branch) {
            $branchesById[$branch['id']] = $branch['name'];
        }

        foreach ($modalities as &$modality) {
            $count = $this->modalityModel->getProductCount($modality['id']);
            $modality['product_count'] = $count['count'] ?? 0;
            $modality['branch_name'] = !empty($modality['branch_id']) ? ($branchesById[$modality['branch_id']] ?? null) : null;
        }

        $this->view->render('plugins/ecommerce/views/admin/modalities', [
            'title' => 'Modalities',
            'modalities' => $modalities
        ], 'admin/views/layout');
    }

    public function create()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $this->view->render('plugins/ecommerce/views/admin/modality-form', [
            'title' => 'Create Modality',
            'modality' => null,
            'branches' => (new Branch())->all()
        ], 'admin/views/layout');
    }

    public function store()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slug = !empty($_POST['slug']) ? $_POST['slug'] : strtolower(str_replace(' ', '-', $_POST['name']));

            $this->modalityModel->create([
                'name' => $_POST['name'],
                'slug' => $slug,
                'icon' => $_POST['icon'] ?? 'fa-desktop',
                'color' => $_POST['color'] ?? 'cyan',
                'branch_id' => !empty($_POST['branch_id']) ? (int) $_POST['branch_id'] : null,
                'display_order' => $_POST['display_order'] ?? 0
            ]);

            $this->redirect('/manager/modalities');
        }
    }

    public function edit($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $modality = $this->modalityModel->find($id);

        $this->view->render('plugins/ecommerce/views/admin/modality-form', [
            'title' => 'Edit Modality',
            'modality' => $modality,
            'branches' => (new Branch())->all()
        ], 'admin/views/layout');
    }

    public function update($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slug = isset($_POST['slug']) ? $_POST['slug'] : strtolower(str_replace(' ', '-', $_POST['name']));

            $this->modalityModel->update($id, [
                'name' => $_POST['name'],
                'slug' => $slug,
                'icon' => $_POST['icon'] ?? 'fa-desktop',
                'color' => $_POST['color'] ?? 'cyan',
                'branch_id' => !empty($_POST['branch_id']) ? (int) $_POST['branch_id'] : null,
                'display_order' => $_POST['display_order'] ?? 0
            ]);

            $this->redirect('/manager/modalities');
        }
    }

    public function delete($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->modalityModel->hasProducts($id)) {
                $_SESSION['error'] = 'Cannot delete modality. It still has products assigned. Please reassign them first.';
                $this->redirect('/manager/modalities');
                return;
            }

            $this->modalityModel->delete($id);
            $_SESSION['success'] = 'Modality deleted successfully.';
        }

        $this->redirect('/manager/modalities');
    }
}
