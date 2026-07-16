<?php

namespace Plugins\Ecommerce\Models;

use Core\Model;

class Category extends Model
{
    protected $table = 'categories';

    public function all()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY display_order ASC, name ASC");
    }

    public function findBySlug($slug)
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE slug = ?", [$slug]);
    }

    public function getProductCount($categoryId)
    {
        return $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products WHERE category_id = ? AND status = 'active'",
            [$categoryId]
        );
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

    // Hierarchical methods
    public function getParentCategories()
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE parent_id IS NULL ORDER BY display_order ASC, name ASC"
        );
    }

    public function getSubcategories($parentId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE parent_id = ? ORDER BY display_order ASC, name ASC",
            [$parentId]
        );
    }

    public function hasSubcategories($categoryId)
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE parent_id = ?",
            [$categoryId]
        );
        return $result['count'] > 0;
    }

    public function getAllWithHierarchy()
    {
        $parents = $this->getParentCategories();
        $allCategories = [];

        foreach ($parents as $parent) {
            $allCategories[] = $parent;
            $subcategories = $this->getSubcategories($parent['id']);
            foreach ($subcategories as $sub) {
                $sub['is_subcategory'] = true;
                $sub['parent_name'] = $parent['name'];
                $allCategories[] = $sub;
            }
        }

        return $allCategories;
    }
}
