<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Core\Auth;
use Plugins\Ecommerce\Models\Order;

class AdminOrderController extends Controller
{
    private $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->orderModel = new Order();
    }

    public function index()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $orders = $this->orderModel->all();

        $this->view->render('plugins/ecommerce/views/admin/orders', [
            'title' => 'Orders',
            'orders' => $orders
        ], 'admin/views/layout');
    }

    public function show($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $order = $this->orderModel->getWithItems($id);

        $this->view->render('plugins/ecommerce/views/admin/order-detail', [
            'title' => 'Order #' . $id,
            'order' => $order
        ], 'admin/views/layout');
    }
}
