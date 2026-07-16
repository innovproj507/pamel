<?php

namespace Plugins\Ecommerce\Models;

use Core\Model;

class ProductReview extends Model
{
    protected $table = 'product_reviews';

    public function getByProduct($productId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE product_id = ? ORDER BY created_at DESC",
            [$productId]
        );
    }

    public function getAverageRating($productId)
    {
        $result = $this->db->fetchOne(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM {$this->table} WHERE product_id = ?",
            [$productId]
        );
        
        return [
            'avg_rating' => round($result['avg_rating'] ?? 0, 2),
            'total_reviews' => $result['total'] ?? 0
        ];
    }

    public function create($data)
    {
        // Insert review
        $reviewId = $this->db->insert($this->table, $data);
        
        // Update product avg_rating and total_reviews
        if ($reviewId && isset($data['product_id'])) {
            $this->updateProductRating($data['product_id']);
        }
        
        return $reviewId;
    }

    public function updateProductRating($productId)
    {
        $stats = $this->getAverageRating($productId);
        
        return $this->db->update('products', [
            'avg_rating' => $stats['avg_rating'],
            'total_reviews' => $stats['total_reviews']
        ], 'id = :id', ['id' => $productId]);
    }

    public function delete($id)
    {
        // Get product_id before deleting
        $review = $this->db->fetchOne("SELECT product_id FROM {$this->table} WHERE id = ?", [$id]);
        
        // Delete review
        $result = $this->db->query("DELETE FROM {$this->table} WHERE id = ?", [$id]);
        
        // Update product rating
        if ($result && $review) {
            $this->updateProductRating($review['product_id']);
        }
        
        return $result;
    }

    public function getRatingDistribution($productId)
    {
        return $this->db->fetchAll(
            "SELECT rating, COUNT(*) as count 
             FROM {$this->table} 
             WHERE product_id = ? 
             GROUP BY rating 
             ORDER BY rating DESC",
            [$productId]
        );
    }
}
