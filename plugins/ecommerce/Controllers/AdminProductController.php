<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Controller;
use Core\Auth;
use Plugins\Ecommerce\Models\Branch;
use Plugins\Ecommerce\Models\Category;
use Plugins\Ecommerce\Models\Product;

class AdminProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }

    public function index()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        // Filters from GET
        $filters = [
            'search'     => trim($_GET['search'] ?? ''),
            'status'     => $_GET['status'] ?? '',
            'category_id'=> $_GET['category_id'] ?? '',
            'branch_id'  => $_GET['branch_id'] ?? '',
        ];
        $filters = array_filter($filters, fn($v) => $v !== '');

        // Pagination
        $page    = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $offset  = ($page - 1) * $perPage;

        $total      = $this->productModel->getTotalCountFiltered($filters);
        $products   = $this->productModel->getPaginatedFiltered($perPage, $offset, $filters);
        $totalPages = (int) ceil($total / $perPage);

        $categories = (new Category())->all();
        $branches   = (new Branch())->all();

        $this->view->render('plugins/ecommerce/views/admin/products', [
            'title'       => 'Products',
            'products'    => $products,
            'categories'  => $categories,
            'branches'    => $branches,
            'filters'     => $filters,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'total'       => $total,
            'perPage'     => $perPage,
        ], 'admin/views/layout');
    }

    public function create()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $this->view->render('plugins/ecommerce/views/admin/product-form', [
            'title' => 'Create Product',
            'product' => null
        ], 'admin/views/layout');
    }

    public function store()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slug = !empty($_POST['slug']) ? $_POST['slug'] : $this->generateSlug($_POST['name']);

            $originalSlug = $slug;
            $counter = 1;
            while ($this->productModel->findBySlug($slug)) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $imagePath = $this->handleImageUpload($_POST['image_url'] ?? '', $_POST['current_image'] ?? '');

            $data = [
                'name'               => $_POST['name'],
                'slug'               => $slug,
                'description'        => $_POST['description'],
                'price'              => $_POST['price'],
                'renewal_price'      => $_POST['renewal_price'] ?? 0,
                'stock'              => $_POST['stock'],
                'category_id'        => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'branch_id'          => !empty($_POST['branch_id']) ? $_POST['branch_id'] : null,
                'modality'           => $_POST['modality'] ?? null,
                'imo_model_course_no'=> !empty($_POST['imo_model_course_no']) ? $_POST['imo_model_course_no'] : null,
                'course_code'        => !empty($_POST['course_code']) ? $_POST['course_code'] : null,
                'duration_hours'     => !empty($_POST['duration_hours']) ? floatval($_POST['duration_hours']) : 0,
                'image'              => $imagePath,
                'status'             => $_POST['status'],
            ];

            $newId = $this->productModel->create($data);
            if ($newId) {
                $this->autoCreateLmsCourse(
                    (int)$newId,
                    $data['name'],
                    $data['description'] ?? '',
                    (float)($data['price'] ?? 0),
                    $data['course_code'] ?? null,
                    !empty($data['branch_id']) ? (int)$data['branch_id'] : null
                );
                $this->redirect('/manager/products');
            } else {
                error_log("Product create failed for: " . ($_POST['name'] ?? ''));
                $this->redirect('/manager/products/create');
            }
        } else {
            $this->redirect('/manager/products/create');
        }
    }

    public function edit($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $product = $this->productModel->find($id);

        $this->view->render('plugins/ecommerce/views/admin/product-form', [
            'title' => 'Edit Product',
            'product' => $product
        ], 'admin/views/layout');
    }

    public function update($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $slug = !empty($_POST['slug']) ? $_POST['slug'] : $this->generateSlug($_POST['name']);
            $imagePath = $this->handleImageUpload($_POST['image_url'] ?? '', $_POST['current_image'] ?? '');
            $branchId = !empty($_POST['branch_id']) ? intval($_POST['branch_id']) : null;

            $this->productModel->update($id, [
                'name' => $_POST['name'],
                'slug' => $slug,
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'renewal_price' => $_POST['renewal_price'] ?? 0,
                'stock' => $_POST['stock'],
                'category_id' => $_POST['category_id'] ?? null,
                'branch_id' => $branchId,
                'modality' => $_POST['modality'] ?? null,
                'imo_model_course_no' => $_POST['imo_model_course_no'] ?? null,
                'course_code' => $_POST['course_code'] ?? null,
                'duration_hours' => !empty($_POST['duration_hours']) ? floatval($_POST['duration_hours']) : 0,
                'image' => $imagePath,
                'status' => $_POST['status']
            ]);

            // If no LMS course linked yet, auto-create one
            $existing = $this->db->fetchOne("SELECT id FROM lms_courses WHERE product_id = ?", [(int)$id]);
            if (!$existing) {
                $this->autoCreateLmsCourse(
                    (int)$id,
                    $_POST['name'],
                    $_POST['description'] ?? '',
                    (float)($_POST['price'] ?? 0),
                    !empty($_POST['course_code']) ? $_POST['course_code'] : null,
                    $branchId
                );
            }

            $this->redirect('/manager/products');
        }
    }

    public function delete($id)
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->productModel->delete($id);
        }

        $this->redirect('/manager/products');
    }
    
    /**
     * Auto-create a draft LMS course linked to a product if none exists.
     */
    /**
     * Maps product branch_id to LMS category_id.
     * branches.id 1 (Pamel) → lms_categories 1 (PAMEL PANAMA)
     * branches.id 2 (Latin Indo) → lms_categories 2 (LATIN INDIA)
     */
    private function mapProductBranchToLms(?int $productBranchId): ?int
    {
        $map = [1 => 1, 2 => 2];
        return $map[$productBranchId] ?? null;
    }

    private function autoCreateLmsCourse(int $productId, string $name, string $description, float $price, ?string $courseCode = null, ?int $productBranchId = null): void
    {
        $existing = $this->db->fetchOne("SELECT id FROM lms_courses WHERE product_id = ?", [$productId]);
        if ($existing) {
            return;
        }

        // Build slug: prefer course_code suffix over numeric suffix for readability
        $base = $this->generateLmsSlug($name);
        if ($courseCode) {
            $slug = $base . '-' . $this->generateLmsSlug($courseCode);
        } else {
            $slug = $base;
        }
        // Ensure uniqueness
        $orig = $slug;
        $n    = 1;
        while ($this->db->fetchOne("SELECT id FROM lms_courses WHERE slug = ?", [$slug])) {
            $slug = $orig . '-' . $n++;
        }

        $this->db->insert('lms_courses', [
            'product_id'      => $productId,
            'category_id'     => $this->mapProductBranchToLms($productBranchId),
            'title'           => $name,
            'slug'            => $slug,
            'description'     => $description,
            'status'          => 'draft',
            'price'           => $price,
            'level'           => 'beginner',
            'pass_percentage' => 70,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * POST /manager/products/sync-courses — create missing LMS courses for all active products.
     */
    public function syncCourses()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/products');
        }

        $products = $this->db->fetchAll(
            "SELECT p.id, p.name, p.description, p.price, p.course_code, p.branch_id
             FROM products p
             LEFT JOIN lms_courses c ON c.product_id = p.id
             WHERE c.id IS NULL AND p.status = 'active'"
        );

        $created = 0;
        foreach ($products as $p) {
            $this->autoCreateLmsCourse(
                (int)$p['id'],
                $p['name'],
                $p['description'] ?? '',
                (float)$p['price'],
                $p['course_code'] ?? null,
                !empty($p['branch_id']) ? (int)$p['branch_id'] : null
            );
            $created++;
        }

        $this->flash('success', "Sincronización completa: {$created} curso(s) creado(s) en borrador.");
        $this->redirect('/manager/lms/courses');
    }

    private function generateLmsSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }

    private function generateSlug($name)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
    
    private function handleImageUpload($imageUrl = '', $currentImage = '')
    {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $mime = mime_content_type($_FILES['image']['tmp_name']);
            if (!in_array($mime, $allowedMimes, true)) {
                error_log("Rejected upload: invalid MIME type $mime");
                return $currentImage;
            }

            $mimeToExt = [
                'image/jpeg' => '.jpg',
                'image/png'  => '.png',
                'image/gif'  => '.gif',
                'image/webp' => '.webp',
            ];
            $ext = $mimeToExt[$mime];

            $projectRoot = dirname(dirname(dirname(__DIR__)));
            $uploadDir = $projectRoot . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (!is_writable($uploadDir)) {
                error_log("Upload directory not writable: $uploadDir");
                return $currentImage;
            }

            $fileName = time() . '_' . bin2hex(random_bytes(8)) . $ext;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                return '/uploads/' . $fileName;
            }

            error_log("Failed to move uploaded file to $targetPath");
        }

        if (!empty($imageUrl)) {
            return $imageUrl;
        }

        return $currentImage;
    }
}
