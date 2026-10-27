<?php

namespace Core;

class Controller
{
    protected $db;
    protected $view;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->view = new View();
    }

    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    protected function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    /**
     * Aviso para la siguiente pantalla.
     *
     * Está aquí porque faltaba: cada sitio que lo necesitaba se lo hacía
     * por su cuenta y dos controladores llamaron a $this->flash() dando
     * por hecho que existía, lo que tumbaba la petición con un error 500
     * justo al terminar el trabajo.
     *
     * La clave es $_SESSION['flash'][tipo], que es la que lee el layout
     * del panel; tipos: success, error, warning, info.
     */
    protected function flash(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$type] = $message;
    }
}
