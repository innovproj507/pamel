<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Plugins\Ecommerce\Models\Cart;
use Plugins\Ecommerce\Models\Order;
use Plugins\Ecommerce\Models\Product;

class OrderController extends Controller
{
    private $cartModel;
    private $orderModel;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->productModel = new Product();
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
            'total' => $total
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
}
