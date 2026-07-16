<?php

namespace Core\Models;

use Core\Model;

class AdmissionRequest extends Model
{
    protected $table = 'admission_requests';

    public function create($data)
    {
        return $this->db->insert($this->table, [
            'given_name' => $data['given_name'],
            'surname' => $data['surname'],
            'passport_id' => $data['passport_id'],
            'nationality' => $data['nationality'],
            'date_of_birth' => $data['date_of_birth'],
            'country_of_birth' => $data['country_of_birth'],
            'email' => $data['email'],
            'address' => $data['address'],
            'capacity' => $data['capacity'] ?? null,
            'phone' => $data['phone'],
            'course' => $data['course'],
            'consent_accepted' => $data['consent_accepted'] ?? 1,
            'id_file' => $data['id_file'] ?? null,
            'health_certificate_file' => $data['health_certificate_file'] ?? null
        ]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->db->fetchOne($sql, [(int)$id]);
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
            $conditions[] = "(given_name LIKE ? OR surname LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
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
            $conditions[] = "(given_name LIKE ? OR surname LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
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
        return $this->db->update(
            $this->table,
            [
                'status' => $status,
                'notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s')
            ],
            'id = :id',
            ['id' => $id]
        );
    }

    public function getStatusCounts()
    {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count
                FROM {$this->table}
                GROUP BY status";
        
        $results = $this->db->fetchAll($sql);
        $counts = [
            'pending' => 0,
            'reviewed' => 0,
            'approved' => 0,
            'rejected' => 0
        ];

        foreach ($results as $row) {
            $counts[$row['status']] = $row['count'];
        }

        return $counts;
    }
}
