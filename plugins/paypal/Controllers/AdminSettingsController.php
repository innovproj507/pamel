<?php
namespace Plugins\Paypal\Controllers;

use Core\Database;
use Core\View;
use Core\Auth;

class AdminSettingsController
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function index()
    {
        // Check admin authentication
        $auth = \Core\Auth::getInstance();
        $auth->requireAdmin('/manager/login');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Load current settings
        $currentSettings = $this->loadSettings();
        
        $view = new View();
        $view->render('plugins/paypal/Views/admin-settings', [
            'settings' => $currentSettings,
            'success' => $_SESSION['paypal_success'] ?? null,
            'error' => $_SESSION['paypal_error'] ?? null
        ], 'admin/views/layout');
        
        unset($_SESSION['paypal_success'], $_SESSION['paypal_error']);
    }
    
    public function save()
    {
        $settings = [
            'paypal_mode' => $_POST['paypal_mode'] ?? 'sandbox',
            'paypal_client_id' => $_POST['paypal_client_id'] ?? '',
            'paypal_client_secret' => $_POST['paypal_client_secret'] ?? '',
            'paypal_merchant_id' => $_POST['paypal_merchant_id'] ?? '',
            'paypal_email' => $_POST['paypal_email'] ?? '',
        ];
        
        foreach ($settings as $key => $value) {
            // Check if setting exists
            $existing = $this->db->fetchOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
            
            if ($existing) {
                // Update
                $this->db->query("UPDATE settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
            } else {
                // Insert
                $this->db->query("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
            }
        }
        
        $_SESSION['paypal_success'] = 'Configuración de PayPal guardada exitosamente';
        header('Location: /manager/paypal-settings');
        exit;
    }
    
    private function loadSettings()
    {
        $settings = [];
        $settingKeys = ['paypal_mode', 'paypal_client_id', 'paypal_client_secret', 'paypal_merchant_id', 'paypal_email'];
        
        foreach ($settingKeys as $key) {
            $result = $this->db->fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
            $settings[$key] = $result ? $result['setting_value'] : '';
        }
        
        return $settings;
    }
}
