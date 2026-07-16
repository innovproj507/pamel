<?php

namespace Controllers;

use Core\Database;
use Core\Config;

class ApiController
{
    /**
     * Returns a list of active products for synchronization with the E-learning system.
     * Requires X-API-KEY header for authentication.
     */
    public function getProducts()
    {
        // Set JSON header
        header('Content-Type: application/json');
        
        // Simple API Key validation
        $apiKey   = $_SERVER['HTTP_X_API_KEY'] ?? $_GET['api_key'] ?? null;
        $validKey = Config::get('app.sync_key', 'PAMEL_SECRET_SYNC_2025');

        if ($apiKey !== $validKey) {
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
            exit;
        }

        try {
            $db = Database::getInstance();
            $baseUrl = rtrim(Config::get('site.url'), '/');
            
            // Fetch only necessary data: id, name, and image
            $products = $db->fetchAll(
                "SELECT id, name, image FROM products WHERE status = 'active' ORDER BY id ASC"
            );
            
            // Format results to include absolute image URL
            $formattedProducts = array_map(function($product) use ($baseUrl) {
                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'image_url' => !empty($product['image']) 
                        ? $baseUrl . '/' . ltrim($product['image'], '/') 
                        : $baseUrl . '/public/assets/images/no-image.jpg'
                ];
            }, $products);
            
            echo json_encode([
                'status' => 'success',
                'count' => count($formattedProducts),
                'data' => $formattedProducts
            ]);
            
        } catch (\Exception $e) {
            error_log('ApiController::getProducts error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Error interno del servidor',
            ]);
        }
        exit;
    }
}
