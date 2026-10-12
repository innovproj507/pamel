<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        \Core\Auth::getInstance()->requireCan('panel.access', '/manager/login');

        // Los indicadores se consultan en la propia vista, como en el resto
        // del panel; aquí solo se resuelve el acceso y se renderiza.
        $this->view->render('admin/views/dashboard', [
            'title' => 'Dashboard',
        ], 'admin/views/layout');
    }
}
