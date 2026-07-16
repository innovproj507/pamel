<?php
namespace Plugins\Paypal\Controllers;

use Core\Database;
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
        $this->db = Database::getInstance();
        $this->initPayPal();
    }
    
    private function getSetting($key, $default = null)
    {
        $result = $this->db->fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $result ? $result['setting_value'] : $default;
    }
    
    private function initPayPal()
    {
        $clientId = $this->getSetting('paypal_client_id');
        $clientSecret = $this->getSetting('paypal_client_secret');
        $mode = $this->getSetting('paypal_mode', 'sandbox');
        
        if (!$clientId || !$clientSecret) {
            return; // PayPal not configured
        }
        
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential($clientId, $clientSecret)
        );
        
        $this->apiContext->setConfig([
            'mode' => $mode,
            'log.LogEnabled' => true,
            'log.FileName' => __DIR__ . '/../../../logs/paypal.log',
            'log.LogLevel' => 'DEBUG'
        ]);
    }
    
    public function createPayment()
    {
        session_start();
        header('Content-Type: application/json');
        
        $orderId = $_POST['order_id'] ?? null;
        
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Order ID required']);
            return;
        }
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            return;
        }
        
        if (!$this->apiContext) {
            echo json_encode(['success' => false, 'message' => 'PayPal not configured']);
            return;
        }
        
        // Get order details
        $order = $this->db->fetchOne(
            "SELECT * FROM orders WHERE id = ? AND user_id = ?",
            [$orderId, $_SESSION['user_id']]
        );
        
        if (!$order) {
            echo json_encode(['success' => false, 'message' => 'Order not found']);
            return;
        }
        
        // Get order items
        $items = $this->db->fetchAll(
            "SELECT * FROM order_items WHERE order_id = ?",
            [$orderId]
        );
        
        try {
            // Create PayPal payment
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');
            
            $itemList = new ItemList();
            $paypalItems = [];
            
            foreach ($items as $item) {
                $paypalItem = new Item();
                $paypalItem->setName($item['product_name'])
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
            
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl($baseUrl . "/payment/success?order_id={$orderId}")
                        ->setCancelUrl($baseUrl . "/payment/cancel?order_id={$orderId}");
            
            $payment = new Payment();
            $payment->setIntent('sale')
                   ->setPayer($payer)
                   ->setRedirectUrls($redirectUrls)
                   ->setTransactions([$transaction]);
            
            $payment->create($this->apiContext);
            
            // Save payment ID to order
            $this->db->query(
                "UPDATE orders SET payment_id = ?, payment_method = 'paypal', payment_status = 'pending' WHERE id = ?",
                [$payment->getId(), $orderId]
            );
            
            echo json_encode([
                'success' => true,
                'approval_url' => $payment->getApprovalLink()
            ]);
        } catch (\Exception $e) {
            error_log('PayPal create payment error: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'PayPal error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function executePayment()
    {
        session_start();
        
        $paymentId = $_GET['paymentId'] ?? null;
        $payerId = $_GET['PayerID'] ?? null;
        $orderId = $_GET['order_id'] ?? null;
        
        if (!$paymentId || !$payerId) {
            $_SESSION['payment_error'] = 'Pago incompleto';
            header('Location: /my-account?tab=orders');
            exit;
        }
        
        try {
            $payment = Payment::get($paymentId, $this->apiContext);
            
            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);
            
            $result = $payment->execute($execution, $this->apiContext);
            
            if ($result->getState() === 'approved') {
                // Update order status
                $this->db->query(
                    "UPDATE orders SET status = 'completed', payment_status = 'paid' WHERE id = ?",
                    [$orderId]
                );
                
                $_SESSION['payment_success'] = '¡Pago completado exitosamente!';
            } else {
                $_SESSION['payment_error'] = 'El pago no fue aprobado';
            }
        } catch (\Exception $e) {
            error_log('PayPal execution error: ' . $e->getMessage());
            $_SESSION['payment_error'] = 'Error al procesar el pago';
        }
        
        header('Location: /my-account?tab=orders');
        exit;
    }
    
    public function cancelPayment()
    {
        session_start();
        $_SESSION['payment_info'] = 'Pago cancelado';
        header('Location: /my-account?tab=orders');
        exit;
    }
}
