<?php

namespace Plugins\Ecommerce\Models;

use Core\Model;

class Branch extends Model
{
    protected $table = 'branches';

    public function all()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY display_order ASC, name ASC");
    }

    public function findBySlug($slug)
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE slug = ?", [$slug]);
    }

    public function getProductCount($branchId)
    {
        return $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE branch_id = ? AND status = 'active'",
            [$branchId]
        );
    }

    public function hasProducts($branchId)
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE branch_id = ?",
            [$branchId]
        );
        return ($result['count'] ?? 0) > 0;
    }

    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db->update($this->table, $data, 'id = :id', ['id' => $id]);
    }

    public function delete($id)
    {
        return $this->db->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
    }
}
