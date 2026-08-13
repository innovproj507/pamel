<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Core\Models\QuoteRequest;
use Plugins\Ecommerce\Models\Cart;
use Plugins\Ecommerce\Models\Product;
use Plugins\Ecommerce\Support\PriceGate;

class ProductController extends Controller
{
    private $productModel;
    private $quoteRequestModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->quoteRequestModel = new QuoteRequest();
    }

    public function index()
    {
        // Get filters from GET parameters
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $branch = isset($_GET['branch']) ? $_GET['branch'] : null;
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
            'branch' => $branch,
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
        
        // Get categories and branches for the filter sidebar
        $categoryModel = new \Plugins\Ecommerce\Models\Category();
        $categories = $categoryModel->all();
        $branchModel = new \Plugins\Ecommerce\Models\Branch();
        $branches = $branchModel->all();
        $modalityModel = new \Plugins\Ecommerce\Models\Modality();
        $modalities = $modalityModel->all();

        $this->view->render('plugins/ecommerce/views/shop', [
            'title' => __('shop.title'),
            'products' => $products,
            'categories' => $categories,
            'branches' => $branches,
            'modalities' => $modalities,
            'filters' => $filters,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProducts' => $totalProducts,
            'priceVisible' => PriceGate::isUnlocked(),
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

        $this->tryUnlockFromQuoteToken($product);

        $this->view->render('plugins/ecommerce/views/product', [
            'title' => $product['name'],
            'product' => $product,
            'priceVisible' => PriceGate::isUnlocked(),
        ], 'public/views/layout');
    }

    /**
     * When a customer follows a "your quote is ready" email link
     * (/courses/:slug?quote=TOKEN), validate the token and, if it matches an
     * approved quote that includes this product, unlock pricing for the
     * session and restore the cart with the quoted items.
     */
    private function tryUnlockFromQuoteToken($product)
    {
        $token = $_GET['quote'] ?? null;
        if (!$token) {
            return;
        }

        $quoteRequest = $this->quoteRequestModel->findByToken($token);
        if (!$quoteRequest || !in_array($quoteRequest['status'], ['quoted', 'completed'], true)) {
            return;
        }

        $items = $this->quoteRequestModel->getItemsByQuoteId($quoteRequest['id']);
        $matchesThisProduct = false;
        foreach ($items as $item) {
            if ((int)$item['product_id'] === (int)$product['id']) {
                $matchesThisProduct = true;
                break;
            }
        }

        if (!$matchesThisProduct) {
            return;
        }

        PriceGate::unlock($quoteRequest['id']);

        $cart = new Cart();
        if ($cart->getCount() == 0) {
            foreach ($items as $item) {
                if ($item['product_id']) {
                    $cart->addItem($item['product_id'], $item['quantity']);
                }
            }
        }
    }
}
