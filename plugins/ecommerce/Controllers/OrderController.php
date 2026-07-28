<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Core\Email;
use Core\Models\QuoteRequest;
use Core\Security;
use Plugins\Ecommerce\Models\Cart;
use Plugins\Ecommerce\Models\Order;
use Plugins\Ecommerce\Models\Product;
use Plugins\Ecommerce\Support\PriceGate;

class OrderController extends Controller
{
    private $cartModel;
    private $orderModel;
    private $productModel;
    private $quoteRequestModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->quoteRequestModel = new QuoteRequest();
    }

    public function checkout()
    {
        $items = $this->cartModel->getItems();
        $total = $this->cartModel->getTotal();

        if (empty($items)) {
            $this->redirect('/shop');
            return;
        }

        $this->view->render('plugins/ecommerce/views/checkout', [
            'title' => 'Checkout',
            'items' => $items,
            'total' => $total,
            'priceVisible' => PriceGate::isUnlocked(),
        ], 'public/views/layout');
    }

    public function process()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/checkout');
            return;
        }

        $items = $this->cartModel->getItems();

        if (empty($items)) {
            $this->redirect('/shop');
            return;
        }

        if (PriceGate::isUnlocked()) {
            $this->processRealOrder($items);
            return;
        }

        $this->processQuoteRequest($items);
    }

    /**
     * Real purchase — unchanged behaviour, only reachable once a quote has been approved
     * and the customer returned via its /shop/:slug?quote=TOKEN link.
     */
    private function processRealOrder($items)
    {
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(419);
            echo 'Invalid or expired form submission. Please go back and try again.';
            return;
        }

        // Create order
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        $orderId = $this->orderModel->createOrder([
            'user_id' => $userId,
            'total_amount' => $this->cartModel->getTotal(),
            'status' => 'pending',
            'customer_name' => $_POST['name'] ?? '',
            'customer_email' => $_POST['email'] ?? '',
            'customer_phone' => $_POST['phone'] ?? '',
            'shipping_address' => $_POST['address'] ?? ''
        ]);

        // Add order items and update stock
        foreach ($items as $item) {
            $this->orderModel->addItem($orderId, [
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);

            // Update product stock
            $this->productModel->updateStock($item['product_id'], $item['quantity']);
        }

        // Clear cart
        $this->cartModel->clear();

        // Redirect to success page
        $this->view->render('plugins/ecommerce/views/success', [
            'title' => 'Order Successful',
            'orderId' => $orderId
        ], 'public/views/layout');
    }

    /**
     * Default path: no price is shown, nothing is charged — the customer only asks
     * for a quote. An admin reviews it under /manager/quote-requests and sends the
     * real price back by email once ready.
     */
    private function processQuoteRequest($items)
    {
        $required = ['name' => 'Nombre', 'email' => 'Email', 'phone' => 'Teléfono'];
        foreach ($required as $field => $label) {
            if (empty($_POST[$field])) {
                $this->renderCheckoutError("El campo \"{$label}\" es requerido.", $items);
                return;
            }
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->renderCheckoutError('Email inválido.', $items);
            return;
        }

        $sanitize = fn($v) => htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');

        $data = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'customer_name' => $sanitize($_POST['name']),
            'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
            'phone' => $sanitize($_POST['phone']),
            'message' => isset($_POST['message']) ? $sanitize($_POST['message']) : null,
        ];

        $quoteRequest = $this->quoteRequestModel->create($data);

        foreach ($items as $item) {
            $this->quoteRequestModel->addItem($quoteRequest['id'], [
                'product_id' => $item['product_id'],
                'product_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price_at_request' => $item['price'],
            ]);
        }

        $emailData = $data;
        $emailData['id'] = $quoteRequest['id'];
        $emailData['items'] = $items;
        Email::sendQuoteRequestNotification($emailData);
        Email::sendQuoteRequestConfirmation($emailData);

        $this->cartModel->clear();

        $this->view->render('plugins/ecommerce/views/quote-success', [
            'title' => 'Quote Request Received',
        ], 'public/views/layout');
    }

    private function renderCheckoutError($message, $items)
    {
        $this->view->render('plugins/ecommerce/views/checkout', [
            'title' => 'Checkout',
            'items' => $items,
            'total' => $this->cartModel->getTotal(),
            'priceVisible' => PriceGate::isUnlocked(),
            'error' => $message,
        ], 'public/views/layout');
    }
}
