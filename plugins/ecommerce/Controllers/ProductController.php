<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Plugins\Ecommerce\Models\Product;

class ProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }

    public function index()
    {
        // Get filters from GET parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $modality = isset($_GET['modality']) ? $_GET['modality'] : null;
        $priceMin = isset($_GET['price_min']) ? $_GET['price_min'] : null;
        $priceMax = isset($_GET['price_max']) ? $_GET['price_max'] : null;
        $durationMin = isset($_GET['duration_min']) ? $_GET['duration_min'] : null;
        $durationMax = isset($_GET['duration_max']) ? $_GET['duration_max'] : null;
        $search = isset($_GET['search']) ? $_GET['search'] : null;
        
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'category' => $category,
            'modality' => $modality,
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'duration_min' => $durationMin,
            'duration_max' => $durationMax,
            'search' => $search
        ];
        
        // Filter out null values
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
        
        // Get products and total count
        $products = $this->productModel->getFrontendPaginated($limit, $offset, $filters);
        $totalProducts = $this->productModel->getTotalFrontendCount($filters);
        $totalPages = ceil($totalProducts / $limit);
        
        // Get categories for the filter sidebar
        $categoryModel = new \Plugins\Ecommerce\Models\Category();
        $categories = $categoryModel->getParentCategories();
        
        $this->view->render('plugins/ecommerce/views/shop', [
            'title' => 'Shop',
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts
        ], 'public/views/layout');
    }

    public function show($slug)
    {
        $product = $this->productModel->findBySlug($slug);

        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }
        
        // Get full product info with category if available
        if (isset($product['id'])) {
            $fullProduct = $this->productModel->getWithCategory($product['id']);
            if ($fullProduct) {
                $product = $fullProduct;
            }
        }

        $this->view->render('plugins/ecommerce/views/product', [
            'title' => $product['name'],
            'product' => $product
        ], 'public/views/layout');
    }
}
