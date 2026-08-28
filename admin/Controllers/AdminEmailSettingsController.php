<?php

namespace Admin\Controllers;

use Core\Auth;
use Core\Database;
use Core\Email;
use Core\View;

class AdminEmailSettingsController
{
    private Database $db;

    private const KEYS = [
        'email_from_name',
        'email_from_email',
        'email_admin_email',
        'email_smtp_enabled',
        'email_smtp_host',
        'email_smtp_port',
        'email_smtp_username',
        'email_smtp_password',
        'email_smtp_encryption',
    ];

    public function __construct()
    {
        \Core\Auth::getInstance()->requireCan('settings.email', '/manager/login');
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $settings = $this->loadSettings();

        $view = new View();
        $view->render('admin/views/email-settings', [
            'title'    => 'Configuración de Email',
            'settings' => $settings,
            'success'  => $_SESSION['email_success'] ?? null,
            'error'    => $_SESSION['email_error'] ?? null,
        ], 'admin/views/layout');

        unset($_SESSION['email_success'], $_SESSION['email_error']);
    }

    public function save(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_POST['csrf_token']) || !\Core\Security::validateCsrfToken($_POST['csrf_token'])) {
            $_SESSION['email_error'] = 'Token de seguridad inválido.';
            header('Location: /manager/email-settings');
            exit;
        }

        $values = [
            'email_from_name'        => trim($_POST['from_name'] ?? ''),
            'email_from_email'       => trim($_POST['from_email'] ?? ''),
            'email_admin_email'      => trim($_POST['admin_email'] ?? ''),
            'email_smtp_enabled'     => isset($_POST['smtp_enabled']) ? '1' : '0',
            'email_smtp_host'        => trim($_POST['smtp_host'] ?? ''),
            'email_smtp_port'        => trim($_POST['smtp_port'] ?? '587'),
            'email_smtp_username'    => trim($_POST['smtp_username'] ?? ''),
            'email_smtp_password'    => trim($_POST['smtp_password'] ?? ''),
            'email_smtp_encryption'  => in_array($_POST['smtp_encryption'] ?? '', ['tls', 'ssl']) ? $_POST['smtp_encryption'] : 'tls',
        ];

        foreach ($values as $key => $value) {
            $existing = $this->db->fetchOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
            if ($existing) {
                $this->db->query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
            } else {
                $this->db->query("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
            }
        }

        $_SESSION['email_success'] = 'Configuración de email guardada exitosamente.';
        header('Location: /manager/email-settings');
        exit;
    }

    public function test(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_POST['csrf_token']) || !\Core\Security::validateCsrfToken($_POST['csrf_token'])) {
            $_SESSION['email_error'] = 'Token de seguridad inválido.';
            header('Location: /manager/email-settings');
            exit;
        }

        $config = $this->loadSettings();
        $to     = $config['admin_email'] ?? '';

        if (empty($to)) {
            $_SESSION['email_error'] = 'Primero configure el email del administrador.';
            header('Location: /manager/email-settings');
            exit;
        }

        $sent = Email::send(
            $to,
            'Correo de prueba - CMS',
            '<p>Este es un correo de prueba enviado desde el panel de administración del CMS.</p>',
            true
        );

        if ($sent) {
            $_SESSION['email_success'] = "Correo de prueba enviado a <strong>{$to}</strong>. Revise su bandeja de entrada.";
        } else {
            $_SESSION['email_error'] = 'Error al enviar el correo de prueba. Verifique la configuración y los logs del servidor.';
        }

        header('Location: /manager/email-settings');
        exit;
    }

    private function loadSettings(): array
    {
        $defaults = [
            'from_name'       => \Core\Config::get('email.from_name', ''),
            'from_email'      => \Core\Config::get('email.from_email', ''),
            'admin_email'     => \Core\Config::get('email.admin_email', ''),
            'smtp_enabled'    => \Core\Config::get('email.smtp_enabled', false),
            'smtp_host'       => \Core\Config::get('email.smtp_host', 'smtp.gmail.com'),
            'smtp_port'       => \Core\Config::get('email.smtp_port', 587),
            'smtp_username'   => \Core\Config::get('email.smtp_username', ''),
            'smtp_password'   => \Core\Config::get('email.smtp_password', ''),
            'smtp_encryption' => \Core\Config::get('email.smtp_encryption', 'tls'),
        ];

        $rows = $this->db->fetchAll(
            "SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'email_%'"
        );

        foreach ($rows as $row) {
            $key   = substr($row['setting_key'], strlen('email_'));
            $value = $row['setting_value'];
            if ($key === 'smtp_enabled') {
                $value = $value === '1';
            } elseif ($key === 'smtp_port') {
                $value = (int) $value;
            }
            $defaults[$key] = $value;
        }

        return $defaults;
    }
}
