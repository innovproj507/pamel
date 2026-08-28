<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $auth = Auth::getInstance();
        \Core\Auth::getInstance()->requireCan('panel.access', '/manager/login');

        // Get statistics
        $stats = [
            'products' => $this->db->fetchOne("SELECT COUNT(*) as count FROM products")['count'],
            'orders' => $this->db->fetchOne("SELECT COUNT(*) as count FROM orders")['count'],
            'revenue' => $this->db->fetchOne("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'")['total'] ?? 0,
            'users' => $this->db->fetchOne("SELECT COUNT(*) as count FROM users")['count']
        ];

        // Recent orders
        $recentOrders = $this->db->fetchAll("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");

        $this->view->render('admin/views/dashboard', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'recentOrders' => $recentOrders
        ], 'admin/views/layout');
    }
}
