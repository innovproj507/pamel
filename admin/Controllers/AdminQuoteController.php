<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Email;
use Core\Models\QuoteRequest;
use Core\Security;
use Plugins\Ecommerce\Models\Product;

class AdminQuoteController extends Controller
{
    private $quoteRequest;
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->quoteRequest = new QuoteRequest();
        $this->productModel = new Product();
    }

    public function index()
    {
        Auth::getInstance()->requireAuth('/manager/login');

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($search) $filters['search'] = $search;

        $requests = $this->quoteRequest->getAllWithPagination($page, 20, $filters);
        foreach ($requests as &$request) {
            $request['items'] = $this->quoteRequest->getItemsByQuoteId($request['id']);
        }
        unset($request);

        $totalCount = $this->quoteRequest->getCount($filters);
        $statusCounts = $this->quoteRequest->getStatusCounts();
        $totalPages = ceil($totalCount / 20);

        $this->view->render('admin/views/quote-requests', [
            'title' => 'Quote Requests',
            'requests' => $requests,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
            'currentSearch' => $search,
        ], 'admin/views/layout');
    }

    public function show($id)
    {
        Auth::getInstance()->requireAuth('/manager/login');

        $request = $this->quoteRequest->getByIdWithItems($id);

        if (!$request) {
            header('Location: /manager/quote-requests');
            exit;
        }

        foreach ($request['items'] as &$item) {
            $product = $item['product_id'] ? $this->productModel->find($item['product_id']) : null;
            $item['current_price'] = $product['price'] ?? null;
            $item['slug'] = $product['slug'] ?? null;
        }
        unset($item);

        $this->view->render('admin/views/quote/show', [
            'title' => 'Quote Request #' . $id,
            'request' => $request,
        ], 'admin/views/layout');
    }

    public function sendQuote($id)
    {
        Auth::getInstance()->requireAuth('/manager/login');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!isset($_POST['csrf_token']) || !Security::validateCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido.']);
            return;
        }

        try {
            $request = $this->quoteRequest->getByIdWithItems($id);
            if (!$request) {
                throw new \Exception('Quote request not found.');
            }
            if ($request['status'] !== 'pending') {
                throw new \Exception('This quote request has already been processed.');
            }

            $items = [];
            foreach ($request['items'] as $item) {
                if (!$item['product_id']) {
                    continue;
                }
                $product = $this->productModel->find($item['product_id']);
                if (!$product) {
                    continue;
                }
                $items[] = [
                    'name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $product['price'],
                    'slug' => $product['slug'],
                ];
            }

            if (empty($items)) {
                throw new \Exception('None of the requested courses could be priced (they may have been removed).');
            }

            Email::sendQuoteReady($request, $items);
            $this->quoteRequest->updateStatus($id, 'quoted');

            echo json_encode(['success' => true, 'message' => 'Quote sent successfully']);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateStatus()
    {
        Auth::getInstance()->requireAuth('/manager/login');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!isset($_POST['csrf_token']) || !Security::validateCsrfToken($_POST['csrf_token'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Token de seguridad inválido.']);
            return;
        }

        try {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $status = isset($_POST['status']) ? $_POST['status'] : '';
            $notes = isset($_POST['notes']) ? $_POST['notes'] : null;

            if (!$id) {
                throw new \Exception('Invalid request ID');
            }

            $validStatuses = ['pending', 'quoted', 'completed', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                throw new \Exception('Invalid status');
            }

            $this->quoteRequest->updateStatus($id, $status, $notes);

            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
