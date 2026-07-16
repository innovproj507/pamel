<?php

namespace Plugins\Ecommerce\Models;

use Core\Model;

class Cart extends Model
{
    protected $table = 'cart';
    private $sessionId;

    public function __construct()
    {
        parent::__construct();
        $this->sessionId = session_id() ?: session_start() && session_id();
    }

    public function getItems()
    {
        return $this->db->fetchAll(
            "SELECT c.*, p.name, p.price, p.image, p.slug 
             FROM {$this->table} c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.session_id = ?",
            [$this->sessionId]
        );
    }

    public function addItem($productId, $quantity = 1)
    {
        // Check if item already exists
        $existing = $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE session_id = ? AND product_id = ?",
            [$this->sessionId, $productId]
        );

        if ($existing) {
            return $this->db->update(
                $this->table,
                ['quantity' => $existing['quantity'] + $quantity],
                'id = :id',
                ['id' => $existing['id']]
            );
        }

        return $this->db->insert($this->table, [
            'session_id' => $this->sessionId,
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
    }

    public function updateQuantity($cartId, $quantity)
    {
        if ($quantity <= 0) {
            return $this->removeItem($cartId);
        }

        return $this->db->update(
            $this->table,
            ['quantity' => $quantity],
            'id = :id AND session_id = :session_id',
            ['id' => $cartId, 'session_id' => $this->sessionId]
        );
    }

    public function removeItem($cartId)
    {
        return $this->db->delete(
            $this->table,
            'id = ? AND session_id = ?',
            [$cartId, $this->sessionId]
        );
    }

    public function getTotal()
    {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function clear()
    {
        return $this->db->delete(
            $this->table,
            'session_id = ?',
            [$this->sessionId]
        );
    }

    public function getCount()
    {
        $result = $this->db->fetchOne(
            "SELECT SUM(quantity) as count FROM {$this->table} WHERE session_id = ?",
            [$this->sessionId]
        );
        return $result['count'] ?? 0;
    }
}
