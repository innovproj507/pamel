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

    /**
     * Exige un token CSRF válido en una petición POST del panel.
     *
     * Si falta o no coincide, deja constancia en el log de seguridad, avisa
     * en la pantalla de origen y corta la petición sin tocar nada. El token
     * viaja en el campo csrf_token del formulario o, para fetch(), en la
     * cabecera X-CSRF-Token.
     */
    protected function requireCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (is_string($token) && $token !== '' && Security::validateCsrfToken($token)) {
            return;
        }

        Security::logSecurityEvent('csrf_validation_failed', [
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
        ]);
        $this->flash('error', 'Token de seguridad inválido. Recarga la página e inténtalo de nuevo.');
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/manager');
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
