<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Plugins\Ecommerce\Models\Cart;

class CartController extends Controller
{
    private $cartModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
    }

    public function index()
    {
        $items = $this->cartModel->getItems();
        $total = $this->cartModel->getTotal();

        $this->view->render('plugins/ecommerce/views/cart', [
            'title' => 'Shopping Cart',
            'items' => $items,
            'total' => $total
        ], 'public/views/layout');
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;

            if ($productId) {
                $this->cartModel->addItem($productId, $quantity);
                $this->redirect('/cart');
            }
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartId = $_POST['cart_id'] ?? null;
            $quantity = $_POST['quantity'] ?? 0;

            if ($cartId) {
                $this->cartModel->updateQuantity($cartId, $quantity);
            }
            
            $this->redirect('/cart');
        }
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartId = $_POST['cart_id'] ?? null;

            if ($cartId) {
                $this->cartModel->removeItem($cartId);
            }
            
            $this->redirect('/cart');
        }
    }
}
