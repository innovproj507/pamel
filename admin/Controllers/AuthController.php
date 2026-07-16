<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        $this->view->render('admin/views/login');
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $auth = Auth::getInstance();
            
            if ($auth->login($email, $password)) {
                $this->redirect('/manager');
            } else {
                $this->view->render('admin/views/login', [
                    'error' => 'Invalid credentials'
                ]);
            }
        }
    }

    public function logout()
    {
        $auth = Auth::getInstance();
        $auth->logout();
        $this->redirect('/manager/login');
    }
}
