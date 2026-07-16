<?php

namespace Plugins\Elearning\Controllers;

use Core\View;
use Plugins\Elearning\Models\Enrollment;

class DashboardController extends BaseController
{
    private $enrollmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->enrollmentModel = new Enrollment();
    }

    public function index()
    {
        $this->requireAuth();
        
        $view = new View();
        $enrollments = $this->enrollmentModel->getByStudent($this->user['id']);

        $view->render('plugins/elearning/Views/dashboard/index', [
            'title'       => 'Mi Dashboard',
            'enrollments' => $enrollments,
            'user'        => $this->user,
        ], 'plugins/elearning/Views/layout');
    }
}
