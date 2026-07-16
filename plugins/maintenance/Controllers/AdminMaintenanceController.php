<?php

namespace Plugins\Maintenance\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Config;

class AdminMaintenanceController extends Controller
{
    private string $flagFile;

    public function __construct()
    {
        parent::__construct();
        $this->flagFile = Config::get('paths.cache') . '/maintenance.flag';
        Auth::getInstance()->requireAdmin();
    }

    public function index(): void
    {
        $isActive = file_exists($this->flagFile);
        $data     = $isActive ? (json_decode(file_get_contents($this->flagFile), true) ?? []) : [];

        $this->view->render(
            'plugins/maintenance/views/admin-settings',
            [
                'title'      => 'Maintenance Mode',
                'isActive'   => $isActive,
                'message'    => $data['message']    ?? "We are performing scheduled maintenance. We'll be back shortly.",
                'returnTime' => $data['return_time'] ?? '',
            ],
            'admin/views/layout'
        );
    }

    public function toggle(): void
    {
        if (file_exists($this->flagFile)) {
            unlink($this->flagFile);
            $_SESSION['flash_success'] = 'Maintenance mode has been <strong>disabled</strong>. The site is now live.';
        } else {
            file_put_contents($this->flagFile, json_encode([
                'message'     => "We are performing scheduled maintenance. We'll be back shortly.",
                'return_time' => '',
                'enabled_at'  => date('Y-m-d H:i:s'),
            ]));
            $_SESSION['flash_success'] = 'Maintenance mode has been <strong>enabled</strong>. The public site is now hidden.';
        }

        $this->redirect('/manager/maintenance');
    }

    public function save(): void
    {
        $message    = trim($_POST['message']     ?? '');
        $returnTime = trim($_POST['return_time'] ?? '');

        if ($message === '') {
            $message = "We are performing scheduled maintenance. We'll be back shortly.";
        }

        $existing = file_exists($this->flagFile)
            ? (json_decode(file_get_contents($this->flagFile), true) ?? [])
            : [];

        file_put_contents($this->flagFile, json_encode([
            'message'     => $message,
            'return_time' => $returnTime,
            'enabled_at'  => $existing['enabled_at'] ?? date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]));

        $_SESSION['flash_success'] = 'Maintenance settings have been saved.';
        $this->redirect('/manager/maintenance');
    }
}
