<?php

namespace Plugins\Ecommerce\Models;

use Core\Model;

class Order extends Model
{
    protected $table = 'orders';

    public function createOrder($data)
    {
        return $this->create($data);
    }

    public function all()
    {
        return $this->db->fetchAll(
            "SELECT o.*, u.name as customer_name, u.email as customer_email 
             FROM {$this->table} o 
             LEFT JOIN users u ON o.user_id = u.id 
             ORDER BY o.created_at DESC"
        );
    }

    public function getWithItems($orderId)
    {
        // Fetch order with customer information from users table
        $order = $this->db->fetchOne(
            "SELECT o.*, 
                    COALESCE(o.customer_name, u.name) as customer_name,
                    COALESCE(o.customer_email, u.email) as customer_email,
                    COALESCE(o.customer_phone, '') as customer_phone,
                    COALESCE(o.shipping_address, '') as shipping_address
             FROM {$this->table} o
             LEFT JOIN users u ON o.user_id = u.id
             WHERE o.id = ?",
            [$orderId]
        );
        
        if ($order) {
            $order['items'] = $this->db->fetchAll(
                "SELECT * FROM order_items WHERE order_id = ?",
                [$orderId]
            );
        }
        return $order;
    }

    public function addItem($orderId, $itemData)
    {
        $itemData['order_id'] = $orderId;
        return $this->db->insert('order_items', $itemData);
    }

    public function updateStatus($orderId, $status)
    {
        return $this->update($orderId, ['status' => $status]);
    }
}
