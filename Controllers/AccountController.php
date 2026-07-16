<?php

namespace Controllers;

use Core\View;
use Core\Models\User;
use Core\Database;
use Core\Security;

class AccountController
{
    private $db;
    private $userModel;

    public function __construct()
    {
        // Incluir seguridad
        require_once __DIR__ . '/../config/security_init.php';
        
        $this->db = Database::getInstance();
        $this->userModel = new User();
    }

    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['user_email'];
        
        // Validar y sanitizar tab
        $currentTab = Security::validateTab($_GET['tab'] ?? 'dashboard');
        
        $data = [
            'currentTab' => $currentTab,
            'user' => $this->userModel->find($userId),
            'orders' => [],
            'downloads' => [],
            'courses' => [],
            'addresses' => [],
            'payment_methods' => [],
            'csrf_token' => Security::generateCsrfToken()
        ];

        // Fetch data based on tab to optimize
        switch ($currentTab) {
            case 'orders':
                $data['orders'] = $this->getOrders($userEmail);
                // Check if viewing specific order
                if (isset($_GET['order_id']) && isset($_GET['source'])) {
                    // Validar order_id
                    $orderId = Security::validateId($_GET['order_id']);
                    if ($orderId) {
                        $data['viewing_order'] = $this->getOrderDetails($orderId, $_GET['source'], $userEmail);
                    }
                }
                break;
            case 'downloads':
                $data['downloads'] = $this->getDownloads($userEmail);
                break;
            case 'my-courses':
                $data['courses'] = $this->getCourses($userEmail);
                break;
            case 'addresses':
                $data['addresses'] = $this->getAddresses($userId);
                break;
            case 'payment-methods':
                $data['payment_methods'] = $this->getPaymentMethods($userId);
                break;
            case 'dashboard':
                $data['orders'] = $this->getOrders($userEmail);
                $data['courses'] = $this->getCourses($userEmail);
                $data['downloads'] = $this->getDownloads($userEmail);
                
                $data['orders_count'] = count($data['orders']);
                $data['downloads_count'] = count($data['downloads']);
                $data['courses_count'] = count($data['courses']);
                
                $data['recommended_courses'] = $this->getRecommendedCourses();
                break;
        }

        $view = new View();
        $view->render('public/views/my-account', $data, 'public/views/layout');
    }

    private function getWpUserId($email)
    {
        return null;
    }

    private function getOrders($email)
    {
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        
        $statusFilter = isset($_GET['status']) && in_array($_GET['status'], ['pending', 'processing', 'completed', 'cancelled']) ? $_GET['status'] : null;

        $sql = "SELECT o.id, o.created_at as date, o.status, o.total_amount as total,
                        (SELECT product_name FROM order_items WHERE order_id = o.id LIMIT 1) as first_item_name,
                        (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
                 FROM orders o
                 WHERE (o.user_id = ? OR o.customer_email = ?)";
                 
        $params = [$userId, $email];
        
        if ($statusFilter) {
            $sql .= " AND o.status = ?";
            $params[] = $statusFilter;
        }

        $sql .= " ORDER BY o.created_at DESC";
                 
        $cmsOrders = $this->db->fetchAll($sql, $params);
        
        $formattedOrders = array_map(function($order) {
            return [
                'id' => $order['id'],
                'date' => $order['date'],
                'status' => $order['status'],
                'total' => $order['total'],
                'currency' => 'USD',
                'source' => 'cms',
                'first_item' => $order['first_item_name'] ?? 'Producto',
                'item_count' => $order['item_count'] ?? 1
            ];
        }, $cmsOrders ?: []);

        return $formattedOrders;
    }

    private function getDownloads($email)
    {
        return [];
    }

    private function getCourses($email)
    {
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        
        $sql = "SELECT DISTINCT p.id, p.name as course_name, p.image, o.id as order_id, o.created_at, p.slug
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                WHERE (o.user_id = ? OR o.customer_email = ?) 
                AND o.status IN ('processing', 'completed')
                ORDER BY o.created_at DESC";
                
        $courses = $this->db->fetchAll($sql, [$userId, $email]);
        
        foreach ($courses as &$course) {
            $course['course_url'] = '/shop/' . $course['slug']; // Direct to shop product for now
        }
        
        return $courses ?: [];
    }

    private function getRecommendedCourses()
    {
        $sql = "
            SELECT id as ID, name as post_title, price, price as regular_price, image
            FROM products 
            WHERE status = 'active' 
            ORDER BY RAND() 
            LIMIT 3
        ";
        return $this->db->fetchAll($sql);
    }

    private function getAddresses($userId)
    {
        if (!$userId) return ['billing' => [], 'shipping' => []];
        $user = $this->db->fetchOne("SELECT billing_address, shipping_address FROM users WHERE id = ?", [$userId]);
        $billing = $user && $user['billing_address'] ? json_decode($user['billing_address'], true) : [];
        $shipping = $user && $user['shipping_address'] ? json_decode($user['shipping_address'], true) : [];
        return ['billing' => is_array($billing) ? $billing : [], 'shipping' => is_array($shipping) ? $shipping : []];
    }

    private function getOrderDetails($orderId, $source, $userEmail)
    {
        // Validar que el pedido pertenece al usuario
        $sql = "SELECT * FROM orders WHERE id = ? AND (customer_email = ? OR user_id = ?)";
        $order = $this->db->fetchOne($sql, [$orderId, $userEmail, $_SESSION['user_id'] ?? 0]);
        
        if (!$order) {
            Security::logSecurityEvent('unauthorized_order_access', [
                'order_id' => $orderId,
                'user_email' => $userEmail
            ]);
            return null;
        }
        
        $items = $this->db->fetchAll("SELECT * FROM order_items WHERE order_id = ?", [$orderId]);
        
        $items = array_map(function($item) {
            if (!isset($item['name']) && isset($item['product_name'])) {
                $item['name'] = $item['product_name'];
            }
            if (!isset($item['total']) && isset($item['price']) && isset($item['quantity'])) {
                $item['total'] = $item['price'] * $item['quantity'];
            }
            return $item;
        }, $items);

        return [
            'id' => $order['id'],
            'date' => $order['created_at'],
            'status' => $order['status'],
            'total' => $order['total_amount'],
            'currency' => 'USD',
            'customer_name' => $order['customer_name'],
            'customer_email' => $order['customer_email'],
            'customer_phone' => $order['customer_phone'],
            'shipping_address' => $order['shipping_address'] ?? '',
            'items' => $items,
            'source' => 'cms'
        ];
    }

    private function getPaymentMethods($userId)
    {
        $sql = "SELECT * FROM payment_methods WHERE user_id = ? AND status = 'active' ORDER BY is_default DESC, created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function updateAddress()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_logged_in'])) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Validar CSRF
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['csrf_token']) || !Security::validateCsrfToken($input['csrf_token'])) {
            Security::logSecurityEvent('csrf_validation_failed', ['action' => 'update_address']);
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            exit;
        }

        // Rate limiting
        if (!Security::checkRateLimit('update_address', 10, 300)) {
            echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again later.']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Validar tipo de dirección
        $type = in_array($input['type'] ?? '', ['billing', 'shipping']) ? $input['type'] : 'billing';
        
        $fields = [
            'first_name', 'last_name', 'company', 'address_1', 'address_2', 
            'city', 'postcode', 'country', 'state', 'phone', 'email'
        ];

        try {
            $user = $this->db->fetchOne("SELECT {$type}_address FROM users WHERE id = ?", [$userId]);
            $address = $user && $user["{$type}_address"] ? json_decode($user["{$type}_address"], true) : [];
            if (!is_array($address)) $address = [];
            
            foreach ($fields as $field) {
                if (isset($input[$field])) {
                    $address[$field] = trim($input[$field]);
                }
            }
            
            $jsonAddress = json_encode($address);
            $sql = "UPDATE users SET {$type}_address = ? WHERE id = ?";
            $this->db->query($sql, [$jsonAddress, $userId]);
            
            Security::logSecurityEvent('address_updated', [
                'user_id' => $userId,
                'type' => $type
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Address updated successfully']);
        } catch (\Exception $e) {
            error_log('Address update error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'An error occurred']);
        }
        exit;
    }

    public function updateDetails()
    {
        if (!isset($_SESSION['user_logged_in'])) {
            header('Location: /login');
            exit;
        }

        // Validate CSRF
        if (!isset($_POST['csrf_token']) || !Security::validateCsrfToken($_POST['csrf_token'])) {
            header('Location: /my-account?tab=account-details&error=' . urlencode('Token de seguridad inválido.'));
            exit;
        }

        $userId = $_SESSION['user_id'];
        $displayName = trim($_POST['display_name'] ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $name = $displayName ?: trim($firstName . ' ' . $lastName);

        $passwordCurrent = $_POST['password_current'] ?? '';
        $passwordNew1 = $_POST['password_1'] ?? '';
        $passwordNew2 = $_POST['password_2'] ?? '';

        try {
            // Update name
            if ($name) {
                $sql = "UPDATE users SET name = ? WHERE id = ?";
                $this->db->query($sql, [$name, $userId]);
                $_SESSION['username'] = $name; // update session username
            }

            // Handle password update if requested
            if (!empty($passwordCurrent) || !empty($passwordNew1) || !empty($passwordNew2)) {
                if (empty($passwordCurrent) || empty($passwordNew1) || empty($passwordNew2)) {
                    throw new \Exception('Debe completar todos los campos de contraseña para cambiarla.');
                }
                
                if ($passwordNew1 !== $passwordNew2) {
                    throw new \Exception('Las nuevas contraseñas no coinciden.');
                }

                $user = $this->userModel->find($userId);
                if (!password_verify($passwordCurrent, $user['password'])) {
                    throw new \Exception('La contraseña actual es incorrecta.');
                }

                $newHash = password_hash($passwordNew1, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password = ? WHERE id = ?";
                $this->db->query($sql, [$newHash, $userId]);
            }

            Security::logSecurityEvent('account_details_updated', ['user_id' => $userId]);
            header('Location: /my-account?tab=account-details&success=1');
            
        } catch (\Exception $e) {
            header('Location: /my-account?tab=account-details&error=' . urlencode($e->getMessage()));
        }
        exit;
    }
}
