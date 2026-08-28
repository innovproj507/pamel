<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Models\AdmissionRequest;
use Core\WordGenerator;

class AdminAdmissionController extends Controller
{
    private $admissionRequest;

    public function __construct()
    {
        parent::__construct();
        $this->admissionRequest = new AdmissionRequest();
    }

    public function index()
    {
        // Require authentication
        $auth = Auth::getInstance();
        \Core\Auth::getInstance()->requireCan('admissions.view', '/manager/login');

        // Get filters from query string
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($search) $filters['search'] = $search;

        // Get admission requests
        $requests = $this->admissionRequest->getAllWithPagination($page, 20, $filters);
        $totalCount = $this->admissionRequest->getCount($filters);
        $statusCounts = $this->admissionRequest->getStatusCounts();

        $totalPages = ceil($totalCount / 20);

        // Render view
        $this->view->render('admin/views/admission-requests', [
            'title' => 'Admission Requests',
            'requests' => $requests,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
            'currentSearch' => $search
        ], 'admin/views/layout');
    }

    public function show($id)
    {
        $auth = Auth::getInstance();
        \Core\Auth::getInstance()->requireCan('admissions.view', '/manager/login');

        $request = $this->admissionRequest->getById($id);

        if (!$request) {
            header('Location: /manager/admission-requests');
            exit;
        }

        $this->view->render('admin/views/admission/show', [
            'title'   => 'Admission Request #' . $id,
            'request' => $request,
        ], 'admin/views/layout');
    }

    public function downloadWord($id)
    {
        $auth = Auth::getInstance();
        \Core\Auth::getInstance()->requireCan('admissions.view', '/manager/login');

        $request = $this->admissionRequest->getById($id);

        if (!$request) {
            header('Location: /manager/admission-requests');
            exit;
        }

        try {
            $filepath = WordGenerator::generateAdmissionDocument($request);

            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="solicitud_admision_' . $id . '.docx"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            ob_clean();
            flush();
            readfile($filepath);
            unlink($filepath);
            exit;

        } catch (\Exception $e) {
            error_log("Error generating admission Word: " . $e->getMessage());
            header('Location: /manager/admission-requests/' . $id);
            exit;
        }
    }

    public function updateStatus()
    {
        // Require authentication
        $auth = Auth::getInstance();
        \Core\Auth::getInstance()->requireCan('admissions.manage', '/manager/login');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        if (!isset($_POST['csrf_token']) || !\Core\Security::validateCsrfToken($_POST['csrf_token'])) {
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

            $validStatuses = ['pending', 'reviewed', 'approved', 'rejected'];
            if (!in_array($status, $validStatuses)) {
                throw new \Exception('Invalid status');
            }

            $this->admissionRequest->updateStatus($id, $status, $notes);

            echo json_encode([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
