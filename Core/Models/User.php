<?php

namespace Core\Models;

use Core\Model;

class User extends Model
{
    protected $table = 'users';

    public function all()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$email]);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} 
                (email, password, name, role) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->query($sql, [
            $data['email'],
            isset($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null,
            $data['name'],
            $data['role'] ?? 'customer'
        ]);
        
        return $stmt !== false;
    }

    public function updateLastLogin($userId)
    {
        $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt !== false;
    }

    public function updateProfile($userId, $data)
    {
        $sql = "UPDATE {$this->table} 
                SET name = ?, email = ?, role = ?, status = ?
                WHERE id = ?";
        
        $stmt = $this->db->query($sql, [
            $data['name'],
            $data['email'],
            $data['role'],
            $data['status'] ?? 'active',
            $userId
        ]);
        
        return $stmt !== false;
    }

    public function toggleStatus($userId)
    {
        $user = $this->find($userId);
        if (!$user) return false;

        $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
        return $this->update($userId, ['status' => $newStatus]);
    }

    public function updatePassword($userId, $newPassword)
    {
        $sql = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $stmt = $this->db->query($sql, [
            password_hash($newPassword, PASSWORD_DEFAULT),
            $userId
        ]);
        return $stmt !== false;
    }

    public function verifyPassword($email, $password)
    {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return false;
        }

        if (password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function paginate($page = 1, $perPage = 10, $filters = [])
    {
        $offset = (int)(($page - 1) * $perPage);
        [$where, $params] = $this->buildFilters($filters);
        $sql = "SELECT * FROM {$this->table} $where ORDER BY created_at DESC LIMIT " . (int)$perPage . " OFFSET $offset";
        return $this->db->fetchAll($sql, $params);
    }

    public function count($filters = [])
    {
        [$where, $params] = $this->buildFilters($filters);
        $result = $this->db->fetchOne("SELECT COUNT(*) as total FROM {$this->table} $where", $params);
        return $result ? (int)$result['total'] : 0;
    }

    private function buildFilters($filters)
    {
        $conditions = [];
        $params     = [];

        if (!empty($filters['search'])) {
            $conditions[] = "(name LIKE ? OR email LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['role'])) {
            $conditions[] = "role = ?";
            $params[] = $filters['role'];
        }
        if (!empty($filters['status'])) {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }
}
