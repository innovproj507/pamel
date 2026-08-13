<?php

namespace Plugins\Ecommerce\Models;

use Core\Model;

class Product extends Model
{
    protected $table = 'products';

    public function getActive()
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY created_at DESC");
    }

    public function findBySlug($slug)
    {
        return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE slug = ?", [$slug]);
    }

    public function search($query)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE (name LIKE ? OR description LIKE ?) AND status = 'active'",
            ["%{$query}%", "%{$query}%"]
        );
    }

    public function getByCategory($category)
    {
        // Get category ID from slug
        $categoryModel = new Category();
        $cat = $categoryModel->findBySlug($category);

        if (!$cat) {
            return [];
        }

        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon,
                    b.name as branch_name, b.slug as branch_slug, b.icon as branch_icon
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN branches b ON p.branch_id = b.id
             WHERE p.category_id = ? AND p.status = 'active'
             ORDER BY p.created_at DESC",
            [$cat['id']]
        );
    }

    public function getByFilters($filters = [])
    {
        // Build dynamic WHERE clause based on filters
        $where = ["p.status = 'active'"];
        $params = [];

        // Filter by category slug
        if (!empty($filters['category'])) {
            $categoryModel = new Category();
            $cat = $categoryModel->findBySlug($filters['category']);
            if ($cat) {
                $where[] = "p.category_id = ?";
                $params[] = $cat['id'];
            }
        }

        // Filter by branch slug
        if (!empty($filters['branch'])) {
            $branchModel = new Branch();
            $branch = $branchModel->findBySlug($filters['branch']);
            if ($branch) {
                $where[] = "p.branch_id = ?";
                $params[] = $branch['id'];
            }
        }

        // Filter by modality
        if (!empty($filters['modality'])) {
            if (is_array($filters['modality'])) {
                // Multiple modalities (B-learning OR E-learning)
                $placeholders = implode(',', array_fill(0, count($filters['modality']), '?'));
                $where[] = "p.modality IN ($placeholders)";
                $params = array_merge($params, $filters['modality']);
            } else {
                // Single modality
                $where[] = "p.modality = ?";
                $params[] = $filters['modality'];
            }
        }

        $whereClause = implode(' AND ', $where);

        return $this->db->fetchAll(
            "SELECT p.*,
                    c.name as category_name, c.slug as category_slug, c.icon as category_icon,
                    b.name as branch_name, b.slug as branch_slug, b.icon as branch_icon
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN branches b ON p.branch_id = b.id
             WHERE $whereClause
             ORDER BY
                COALESCE(b.display_order, 999) ASC,
                p.created_at DESC",
            $params
        );
    }

    public function updateStock($id, $quantity)
    {
        return $this->db->query(
            "UPDATE {$this->table} SET stock = stock - ? WHERE id = ?",
            [$quantity, $id]
        );
    }

    public function getWithCategory($id)
    {
        return $this->db->fetchOne(
            "SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon,
                    b.name as branch_name, b.slug as branch_slug, b.icon as branch_icon,
                    m.name as modality_name, m.slug as modality_slug, m.icon as modality_icon, m.color as modality_color
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN branches b ON p.branch_id = b.id
             LEFT JOIN modalities m ON p.modality_id = m.id
             WHERE p.id = ?",
            [$id]
        );
    }

    public function getAllWithCategories()
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug,
                    b.name as branch_name, b.slug as branch_slug
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN branches b ON p.branch_id = b.id
             WHERE p.status = 'active'
             ORDER BY
                COALESCE(b.display_order, 999) ASC,
                p.created_at DESC"
        );
    }

    public function getRelatedProducts($productId, $limit = 4)
    {
        return $this->db->fetchAll(
            "SELECT p.* FROM {$this->table} p
             INNER JOIN related_products rp ON p.id = rp.related_product_id
             WHERE rp.product_id = ? AND p.status = 'active'
             ORDER BY rp.display_order ASC
             LIMIT ?",
            [$productId, $limit]
        );
    }

    public function addRelatedProduct($productId, $relatedProductId, $displayOrder = 0)
    {
        return $this->db->insert('related_products', [
            'product_id' => $productId,
            'related_product_id' => $relatedProductId,
            'display_order' => $displayOrder
        ]);
    }

    public function removeRelatedProduct($productId, $relatedProductId)
    {
        return $this->db->query(
            "DELETE FROM related_products WHERE product_id = ? AND related_product_id = ?",
            [$productId, $relatedProductId]
        );
    }

    public function getSimilarByCategory($productId, $categoryId, $limit = 4)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE category_id = ? AND id != ? AND status = 'active'
             ORDER BY avg_rating DESC, created_at DESC
             LIMIT ?",
            [$categoryId, $productId, $limit]
        );
    }

    public function getAllWithCategoriesForAdmin()
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, c.icon as category_icon
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             ORDER BY p.created_at DESC"
        );
    }

    public function getTotalCount()
    {
        $result = $this->db->fetchOne("SELECT COUNT(*) as count FROM {$this->table}");
        return $result['count'] ?? 0;
    }

    public function getTotalCountFiltered(array $filters = []): int
    {
        [$where, $params] = $this->buildAdminWhere($filters);
        $sql    = "SELECT COUNT(*) as count FROM {$this->table} p LEFT JOIN categories c ON p.category_id = c.id $where";
        $result = $this->db->fetchOne($sql, $params);
        return (int) ($result['count'] ?? 0);
    }

    public function getPaginatedWithCategories($limit, $offset)
    {
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, c.icon as category_icon
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }

    public function getPaginatedFiltered(int $limit, int $offset, array $filters = []): array
    {
        [$where, $params] = $this->buildAdminWhere($filters);
        $params[] = $limit;
        $params[] = $offset;
        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, c.icon as category_icon, lc.id as lms_course_id
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN lms_courses lc ON lc.product_id = p.id
             $where
             ORDER BY p.created_at DESC
             LIMIT ? OFFSET ?",
            $params
        );
    }

    public function getFrontendPaginated($limit, $offset, $filters = [])
    {
        [$where, $params] = $this->buildFrontendWhere($filters);

        $params[] = $limit;
        $params[] = $offset;

        return $this->db->fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon,
                    b.name as branch_name, b.slug as branch_slug, b.icon as branch_icon,
                    m.name as modality_name, m.slug as modality_slug, m.icon as modality_icon, m.color as modality_color
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN branches b ON p.branch_id = b.id
             LEFT JOIN modalities m ON p.modality_id = m.id
             $where
             ORDER BY
                COALESCE(b.display_order, 999) ASC,
                p.created_at DESC
             LIMIT ? OFFSET ?",
            $params
        );
    }

    public function getTotalFrontendCount($filters = [])
    {
        [$where, $params] = $this->buildFrontendWhere($filters);
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as count
             FROM {$this->table} p
             LEFT JOIN categories c ON p.category_id = c.id
             LEFT JOIN branches b ON p.branch_id = b.id
             LEFT JOIN modalities m ON p.modality_id = m.id
             $where",
            $params
        );
        return $result['count'] ?? 0;
    }

    private function buildFrontendWhere($filters)
    {
        $where = ["p.status = 'active'"];
        $params = [];

        if (!empty($filters['category'])) {
            $where[] = "c.slug = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['branch'])) {
            $where[] = "b.slug = ?";
            $params[] = $filters['branch'];
        }

        if (!empty($filters['modality'])) {
            $where[] = "m.slug = ?";
            $params[] = $filters['modality'];
        }

        if (!empty($filters['price_min'])) {
            $where[] = "p.price >= ?";
            $params[] = (float)$filters['price_min'];
        }

        if (!empty($filters['price_max'])) {
            $where[] = "p.price <= ?";
            $params[] = (float)$filters['price_max'];
        }

        if (!empty($filters['duration_min'])) {
            $where[] = "p.duration_hours >= ?";
            $params[] = (int)$filters['duration_min'];
        }

        if (!empty($filters['duration_max'])) {
            $where[] = "p.duration_hours <= ?";
            $params[] = (int)$filters['duration_max'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $term = "%{$filters['search']}%";
            $params[] = $term;
            $params[] = $term;
        }

        $whereClause = "WHERE " . implode(' AND ', $where);
        return [$whereClause, $params];
    }

    private function buildAdminWhere(array $filters): array
    {
        $conditions = [];
        $params     = [];

        if (!empty($filters['search'])) {
            $conditions[] = "(p.name LIKE ? OR p.course_code LIKE ?)";
            $term         = '%' . $filters['search'] . '%';
            $params[]     = $term;
            $params[]     = $term;
        }
        if (!empty($filters['status'])) {
            $conditions[] = "p.status = ?";
            $params[]     = $filters['status'];
        }
        if (!empty($filters['category_id'])) {
            $conditions[] = "p.category_id = ?";
            $params[]     = (int) $filters['category_id'];
        }
        if (!empty($filters['branch_id'])) {
            $conditions[] = "p.branch_id = ?";
            $params[]     = (int) $filters['branch_id'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }
}
