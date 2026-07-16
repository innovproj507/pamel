<?php
namespace Controllers;

use Core\Database;
use Core\Security;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;
use PayPal\Api\Amount;
use PayPal\Api\Payer;
use PayPal\Api\RedirectUrls;
use PayPal\Api\ItemList;
use PayPal\Api\Item;

class PaymentController
{
    private $db;
    private $apiContext;
    
    public function __construct()
    {
        // Incluir seguridad
        require_once __DIR__ . '/../config/security_init.php';
        
        $this->db = Database::getInstance();
        $this->initPayPal();
    }
    
    private function initPayPal()
    {
        $config = require __DIR__ . '/../config/paypal.php';
        
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                $config['client_id'],
                $config['client_secret']
            )
        );
        
        // Configuración segura para producción
        $isProduction = (getenv('APP_ENV') === 'production');
        
        $this->apiContext->setConfig([
            'mode' => $isProduction ? 'live' : $config['mode'],
            'log.LogEnabled' => !$isProduction, // Solo en desarrollo
            'log.FileName' => __DIR__ . '/../logs/paypal.log',
            'log.LogLevel' => $isProduction ? 'ERROR' : 'DEBUG'
        ]);
    }
    
    public function createPayment()
    {
        session_start();
        header('Content-Type: application/json');
        
        // Rate limiting
        if (!Security::checkRateLimit('create_payment', 5, 300)) {
            echo json_encode(['success' => false, 'message' => 'Demasiados intentos. Intente más tarde.']);
            exit;
        }
        
        // Validar usuario autenticado
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            exit;
        }
        
        // Validar order_id
        $orderId = Security::validateId($_POST['order_id'] ?? null);
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'ID de pedido inválido']);
            exit;
        }
        
        // Obtener y validar que el pedido pertenece al usuario
        $order = $this->db->fetchOne(
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
            [$orderId, $_SESSION['user_id']]
        );
        
        if (!$order) {
            Security::logSecurityEvent('unauthorized_payment_attempt', [
                'order_id' => $orderId,
                'user_id' => $_SESSION['user_id']
            ]);
            echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
            exit;
        }
        
        // Obtener items del pedido
        $items = $this->db->fetchAll(
            "SELECT * FROM order_items WHERE order_id = ?",
            [$orderId]
        );
        
        // Validar que el total calculado coincide con el total del pedido
        $calculatedTotal = 0;
        foreach ($items as $item) {
            $calculatedTotal += $item['price'] * $item['quantity'];
        }
        
        if (abs($calculatedTotal - $order['total_amount']) > 0.01) {
            Security::logSecurityEvent('order_total_mismatch', [
                'order_id' => $orderId,
                'stored_total' => $order['total_amount'],
                'calculated_total' => $calculatedTotal
            ]);
            echo json_encode(['success' => false, 'message' => 'Error en el total del pedido']);
            exit;
        }
        
        try {
            // Crear pago de PayPal
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');
            
            $itemList = new ItemList();
            $paypalItems = [];
            
            foreach ($items as $item) {
                $paypalItem = new Item();
                $paypalItem->setName(Security::escape($item['product_name']))
                          ->setCurrency('USD')
                          ->setQuantity($item['quantity'])
                          ->setPrice($item['price']);
                $paypalItems[] = $paypalItem;
            }
            
            $itemList->setItems($paypalItems);
            
            $amount = new Amount();
            $amount->setCurrency('USD')
                   ->setTotal($order['total_amount']);
            
            $transaction = new Transaction();
            $transaction->setAmount($amount)
                       ->setItemList($itemList)
                       ->setDescription("Order #ORD-{$orderId}")
                       ->setInvoiceNumber("ORD-{$orderId}");
            
            $config = require __DIR__ . '/../config/paypal.php';
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl($config['return_url'] . "?order_id={$orderId}")
                        ->setCancelUrl($config['cancel_url'] . "?order_id={$orderId}");
            
            $payment = new Payment();
            $payment->setIntent('sale')
                   ->setPayer($payer)
                   ->setRedirectUrls($redirectUrls)
                   ->setTransactions([$transaction]);
            
            $payment->create($this->apiContext);
            
            // Guardar payment ID
            $this->db->query(
                "UPDATE orders SET payment_id = ?, payment_method = 'paypal', payment_status = 'pending' WHERE id = ?",
                [$payment->getId(), $orderId]
            );
            
            Security::logSecurityEvent('payment_created', [
                'order_id' => $orderId,
                'payment_id' => $payment->getId(),
                'amount' => $order['total_amount']
            ]);
            
            echo json_encode([
                'success' => true,
                'approval_url' => $payment->getApprovalLink()
            ]);
        } catch (\Exception $e) {
            error_log('PayPal create payment error: ' . $e->getMessage());
            Security::logSecurityEvent('payment_creation_failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            echo json_encode([
                'success' => false,
                'message' => 'Error al procesar el pago. Por favor intente nuevamente.'
            ]);
        }
    }
    
    public function executePayment()
    {
        session_start();
        
        $paymentId = $_GET['paymentId'] ?? null;
        $payerId = $_GET['PayerID'] ?? null;
        $orderId = Security::validateId($_GET['order_id'] ?? null);
        
        if (!$paymentId || !$payerId || !$orderId) {
            header('Location: /my-account?tab=orders&error=payment_failed');
            exit;
        }
        
        // Validar que el pedido pertenece al usuario
        $order = $this->db->fetchOne(
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
            [$orderId, $_SESSION['user_id'] ?? 0]
        );
        
        if (!$order) {
            Security::logSecurityEvent('unauthorized_payment_execution', [
                'order_id' => $orderId,
                'payment_id' => $paymentId
            ]);
            header('Location: /my-account?tab=orders&error=unauthorized');
            exit;
        }
        
        try {
            $payment = Payment::get($paymentId, $this->apiContext);
            
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);
            
            $result = $payment->execute($execution, $this->apiContext);
            
            if ($result->getState() === 'approved') {
                // Actualizar estado del pedido
                $this->db->query(
                    "UPDATE orders SET status = 'completed', payment_status = 'paid' WHERE id = ?",
                    [$orderId]
                );
                
                Security::logSecurityEvent('payment_completed', [
                    'order_id' => $orderId,
                    'payment_id' => $paymentId,
                    'amount' => $order['total_amount']
                ]);
                
                $_SESSION['payment_success'] = 'Pago completado exitosamente';
                header('Location: /my-account?tab=orders&success=1');
            } else {
                Security::logSecurityEvent('payment_not_approved', [
                    'order_id' => $orderId,
                    'payment_id' => $paymentId,
                    'state' => $result->getState()
                ]);
                
                $_SESSION['payment_error'] = 'El pago no fue aprobado';
                header('Location: /my-account?tab=orders&error=1');
            }
        } catch (\Exception $e) {
            error_log('PayPal execution error: ' . $e->getMessage());
            Security::logSecurityEvent('payment_execution_failed', [
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            
            $_SESSION['payment_error'] = 'Error al procesar el pago';
            header('Location: /my-account?tab=orders&error=1');
        }
        exit;
    }
    
    public function cancelPayment()
    {
        session_start();
        $orderId = Security::validateId($_GET['order_id'] ?? null);
        
        if ($orderId) {
            Security::logSecurityEvent('payment_cancelled', [
                'order_id' => $orderId
            ]);
        }
        
        $_SESSION['payment_info'] = 'Pago cancelado';
        header('Location: /my-account?tab=orders&cancelled=1');
        exit;
    }
    
    public function processPayment()
    {
        session_start();
        header('Content-Type: application/json');
        
        // Validar usuario autenticado
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
            exit;
        }
        
        // Obtener datos JSON
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validar CSRF
        if (!isset($input['csrf_token']) || !Security::validateCsrfToken($input['csrf_token'])) {
            Security::logSecurityEvent('csrf_validation_failed', ['action' => 'process_payment']);
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido']);
            exit;
        }
        
        // Rate limiting
        if (!Security::checkRateLimit('process_payment', 5, 300)) {
            echo json_encode(['success' => false, 'message' => 'Demasiados intentos. Intente más tarde.']);
            exit;
        }
        
        // Validar order_id
        $orderId = Security::validateId($input['order_id'] ?? null);
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'ID de pedido inválido']);
            exit;
        }
        
        // Validar método de pago
        $allowedMethods = ['credit_card', 'paypal', 'transfer'];
        $method = in_array($input['method'] ?? '', $allowedMethods) ? $input['method'] : null;
        
        if (!$method) {
            echo json_encode(['success' => false, 'message' => 'Método de pago inválido']);
            exit;
        }
        
        // Validar que el pedido pertenece al usuario
        $order = $this->db->fetchOne(
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
            [$orderId, $_SESSION['user_id']]
        );
        
        if (!$order) {
            Security::logSecurityEvent('unauthorized_payment_process', [
                'order_id' => $orderId,
                'method' => $method
            ]);
            echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
            exit;
        }
        
        // Procesar según el método
        try {
            switch ($method) {
                case 'transfer':
                    // Actualizar estado a pendiente de verificación
                    $this->db->query(
                        "UPDATE orders SET payment_method = 'transfer', payment_status = 'pending_verification', status = 'on-hold' WHERE id = ?",
                        [$orderId]
                    );
                    
                    Security::logSecurityEvent('transfer_reported', [
                        'order_id' => $orderId,
                        'reference' => $input['data']['reference'] ?? 'N/A'
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Transferencia reportada. Verificaremos el pago en breve.'
                    ]);
                    break;
                    
                case 'paypal':
                    // Redirigir a createPayment
                    echo json_encode([
                        'success' => true,
                        'redirect' => '/payment/create?order_id=' . $orderId
                    ]);
                    break;
                    
                case 'credit_card':
                    // Aquí iría la integración con procesador de tarjetas (Stripe, etc.)
                    echo json_encode([
                        'success' => false,
                        'message' => 'Procesamiento de tarjetas en desarrollo'
                    ]);
                    break;
            }
        } catch (\Exception $e) {
            error_log('Payment processing error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error al procesar el pago'
            ]);
        }
        exit;
    }
}
