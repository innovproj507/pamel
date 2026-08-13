<?php

namespace Plugins\Ecommerce\Models;

use Core\Model;

class Modality extends Model
{
    protected $table = 'modalities';

    public function all()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY display_order ASC, name ASC");
    }

    public function findBySlug($slug)
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE slug = ?", [$slug]);
    }

    public function getProductCount($modalityId)
    {
        return $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE modality_id = ? AND status = 'active'",
            [$modalityId]
        );
    }

    public function hasProducts($modalityId)
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE modality_id = ?",
            [$modalityId]
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
