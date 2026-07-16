<?php

namespace Core\Models;

use Core\Model;

class ContactMessage extends Model
{
    protected $table = 'contact_messages';

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (name, email, phone, subject, message, status) VALUES (:name, :email, :phone, :subject, :message, :status)";
        $params = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'new'
        ];
        
        return $this->db->query($sql, $params);
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY created_at DESC");
    }

    public function getUnreadCount()
    {
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'new'");
        return $result['count'] ?? 0;
    }
    
    public function markAsRead($id)
    {
        return $this->db->query("UPDATE {$this->table} SET status = 'read' WHERE id = ?", [$id]);
    }
}
