<?php

namespace Core\Models;

use Core\Model;

class QuoteRequest extends Model
{
    protected $table = 'quote_requests';

    public function create($data)
    {
        $token = bin2hex(random_bytes(24));

        $id = $this->db->insert($this->table, [
            'token' => $token,
            'user_id' => $data['user_id'] ?? null,
            'customer_name' => $data['customer_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'message' => $data['message'] ?? null,
            'status' => 'pending',
        ]);

        return ['id' => $id, 'token' => $token];
    }

    public function addItem($quoteRequestId, array $item)
    {
        return $this->db->insert('quote_request_items', [
            'quote_request_id' => $quoteRequestId,
            'product_id' => $item['product_id'] ?? null,
            'product_name' => $item['product_name'],
            'quantity' => $item['quantity'] ?? 1,
            'price_at_request' => $item['price_at_request'] ?? null,
        ]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->db->fetchOne($sql, [(int)$id]);
    }

    public function getByIdWithItems($id)
    {
        $quoteRequest = $this->getById($id);
        if (!$quoteRequest) {
            return null;
        }

        $quoteRequest['items'] = $this->getItemsByQuoteId($id);
        return $quoteRequest;
    }

    public function findByToken($token)
    {
        $sql = "SELECT * FROM {$this->table} WHERE token = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$token]);
    }

    public function getItemsByQuoteId($quoteRequestId)
    {
        $sql = "SELECT * FROM quote_request_items WHERE quote_request_id = ? ORDER BY id";
        return $this->db->fetchAll($sql, [(int)$quoteRequestId]);
    }

    public function getAllWithPagination($page = 1, $perPage = 20, $filters = [])
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(customer_name LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT * FROM {$this->table}
                {$where}
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";

        $params[] = $perPage;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function getCount($filters = [])
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(customer_name LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT COUNT(*) as count FROM {$this->table} {$where}";

        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] ?? 0;
    }

    public function updateStatus($id, $status, $notes = null)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($notes !== null) {
            $data['notes'] = $notes;
        }

        if ($status === 'quoted') {
            $data['quoted_at'] = date('Y-m-d H:i:s');
        }

        return $this->db->update(
            $this->table,
            $data,
            'id = :id',
            ['id' => $id]
        );
    }

    public function getStatusCounts()
    {
        $sql = "SELECT status, COUNT(*) as count FROM {$this->table} GROUP BY status";

        $results = $this->db->fetchAll($sql);
        $counts = [
            'pending' => 0,
            'quoted' => 0,
            'completed' => 0,
            'cancelled' => 0,
        ];

        foreach ($results as $row) {
            $counts[$row['status']] = $row['count'];
        }

        return $counts;
    }
}
