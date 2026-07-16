<?php
/**
 * Security Helper Functions
 * Funciones de seguridad para proteger la aplicación
 */

namespace Core;

class Security
{
    /**
     * Generar token CSRF
     */
    public static function generateCsrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validar token CSRF
     */
    public static function validateCsrfToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Obtener token CSRF para formularios
     */
    public static function getCsrfField()
    {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Sanitizar salida HTML
     */
    public static function escape($string)
    {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validar ID numérico
     */
    public static function validateId($id)
    {
        $validated = filter_var($id, FILTER_VALIDATE_INT);
        if ($validated === false || $validated <= 0) {
            return false;
        }
        return $validated;
    }
    
    /**
     * Validar email
     */
    public static function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * Sanitizar string
     */
    public static function sanitizeString($string)
    {
        if ($string === null) {
            return '';
        }
        return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validar tab permitido
     */
    public static function validateTab($tab)
    {
        $allowedTabs = [
            'dashboard',
            'orders',
            'downloads',
            'addresses',
            'payment-methods',
            'account-details',
            'my-courses'
        ];
        
        return in_array($tab, $allowedTabs) ? $tab : 'dashboard';
    }
    
    /**
     * Configurar sesiones seguras
     */
    public static function configureSecureSessions()
    {
        // Solo configurar si la sesión no ha iniciado
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            // Solo en producción con HTTPS
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            
            // Regenerar ID de sesión periódicamente
            ini_set('session.gc_maxlifetime', 3600); // 1 hora
        }
    }
    
    /**
     * Forzar HTTPS en producción
     */
    public static function forceHttps()
    {
        // Solo forzar en producción
        if (getenv('APP_ENV') === 'production') {
            if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
                header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }
    
    /**
     * Log de eventos de seguridad
     */
    public static function logSecurityEvent($event, $details = [])
    {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $log = sprintf(
            "[%s] User: %s, IP: %s, Event: %s, Details: %s\n",
            date('Y-m-d H:i:s'),
            $_SESSION['user_id'] ?? 'guest',
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $event,
            json_encode($details)
        );
        
        file_put_contents($logDir . '/security.log', $log, FILE_APPEND);
    }
    
    /**
     * Rate limiting simple
     */
    public static function checkRateLimit($action, $maxAttempts = 5, $timeWindow = 300)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $key = 'rate_limit_' . $action;
        $now = time();
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 1, 'start' => $now];
            return true;
        }
        
        $data = $_SESSION[$key];
        
        // Resetear si pasó el tiempo
        if ($now - $data['start'] > $timeWindow) {
            $_SESSION[$key] = ['count' => 1, 'start' => $now];
            return true;
        }
        
        // Incrementar contador
        $_SESSION[$key]['count']++;
        
        // Verificar límite
        if ($_SESSION[$key]['count'] > $maxAttempts) {
            self::logSecurityEvent('rate_limit_exceeded', [
                'action' => $action,
                'attempts' => $_SESSION[$key]['count']
            ]);
            return false;
        }
        
        return true;
    }
}
