<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Models\AdmissionRequest;
use Core\WordGenerator;

class AdminAdmissionController extends Controller
{
    /** Opciones de filas por pantalla que ofrece la lista. */
    private const POR_PAGINA = [5, 10, 15, 25, 50];

    private const POR_PAGINA_DEFECTO = 15;

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

        // Cuántas filas por pantalla. Sólo se admiten las de la lista: un
        // valor libre en la URL permitiría pedir cien mil de golpe.
        $perPage = (int) ($_GET['per_page'] ?? self::POR_PAGINA_DEFECTO);
        if (!in_array($perPage, self::POR_PAGINA, true)) {
            $perPage = self::POR_PAGINA_DEFECTO;
        }

        $filters = [];
        if ($status) $filters['status'] = $status;
        if ($search) $filters['search'] = $search;

        $totalCount = $this->admissionRequest->getCount($filters);
        $totalPages = max(1, (int) ceil($totalCount / $perPage));

        // Si al reducir las filas por pantalla la página actual se queda
        // fuera de rango, se vuelve a la última que existe.
        $page = max(1, min($page, $totalPages));

        $requests     = $this->admissionRequest->getAllWithPagination($page, $perPage, $filters);
        $statusCounts = $this->admissionRequest->getStatusCounts();

        // Render view
        $this->view->render('admin/views/admission-requests', [
            'title' => 'Admission Requests',
            'requests' => $requests,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCount' => $totalCount,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
            'currentSearch' => $search,
            'perPage' => $perPage,
            'perPageOptions' => self::POR_PAGINA,
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

        // Documentos adjuntados por requisito
        $requisitos = $this->db->fetchAll(
            "SELECT requirement_id, label, file_path, original_name, size_bytes, uploaded_at
             FROM admission_request_files
             WHERE admission_request_id = ?
             ORDER BY id ASC",
            [(int) $id]
        );

        $this->view->render('admin/views/admission/show', [
            'requirementFiles' => $requisitos,
            // Una solicitud puede llevar varios cursos, cada uno con su
            // modalidad; la columna `course` sólo guarda el resumen.
            'requestCourses'   => $this->admissionRequest->getCourses((int) $id),
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

        // Los cursos van aparte desde que una solicitud puede llevar varios;
        // sin esto el formulario saldría con una sola casilla marcada.
        $request['courses'] = $this->admissionRequest->getCourses((int) $id);

        try {
            $rutas = WordGenerator::generateAdmissionDocuments($request);
            if (!$rutas) {
                throw new \Exception('No se pudo generar el documento.');
            }

            // Mezclar cursos de logística con el resto da dos formularios, el
            // F-41 y el F-46: no caben en el mismo papel, así que van en zip.
            if (count($rutas) > 1) {
                $this->descargarZip($rutas, 'solicitud_admision_' . $id . '.zip');
            }

            $filepath = $rutas[0];

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

    /**
     * Envía varios .docx en un zip y termina la petición.
     *
     * Los temporales se borran siempre, haya salido bien o mal: son datos
     * personales del solicitante y no deben quedarse en el servidor.
     *
     * @param string[] $rutas
     */
    private function descargarZip(array $rutas, string $nombre): void
    {
        $zipPath = tempnam(sys_get_temp_dir(), 'adm') . '.zip';
        $zip     = new \ZipArchive();

        try {
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('No se pudo crear el zip de la solicitud.');
            }
            foreach ($rutas as $ruta) {
                $zip->addFile($ruta, basename($ruta));
            }
            $zip->close();

            header('Content-Description: File Transfer');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $nombre . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($zipPath));

            ob_clean();
            flush();
            readfile($zipPath);
        } finally {
            foreach ($rutas as $ruta) {
                if (is_file($ruta)) { @unlink($ruta); }
            }
            if (is_file($zipPath)) { @unlink($zipPath); }
        }

        exit;
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

    /**
     * Elimina una solicitud de admisión junto con sus adjuntos.
     *
     * No tiene vuelta atrás, así que exige POST con CSRF y el permiso de
     * gestión; la confirmación la pide el listado antes de enviar.
     */
    public function delete()
    {
        \Core\Auth::getInstance()->requireCan('admissions.manage', '/manager/login');

        $volver = '/manager/admission-requests';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir($volver, 'error', 'Método no permitido.');
        }

        if (!isset($_POST['csrf_token']) || !\Core\Security::validateCsrfToken($_POST['csrf_token'])) {
            $this->redirigir($volver, 'error', 'Token de seguridad inválido.');
        }

        // Se conservan los filtros de la pantalla desde la que se borró.
        if (!empty($_POST['volver_a']) && str_starts_with((string) $_POST['volver_a'], '/manager/admission-requests')) {
            $volver = (string) $_POST['volver_a'];
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->redirigir($volver, 'error', 'Solicitud no válida.');
        }

        $solicitud = $this->admissionRequest->getById($id);
        if (!$solicitud) {
            $this->redirigir($volver, 'error', 'La solicitud no existe o ya fue eliminada.');
        }

        $nombre = trim(($solicitud['given_name'] ?? '') . ' ' . ($solicitud['surname'] ?? ''));
        $r = $this->admissionRequest->deleteWithFiles($id);

        if (!$r['borrada']) {
            $this->redirigir($volver, 'error', 'No se pudo eliminar la solicitud. Revisa el log de errores de PHP.');
        }

        $mensaje = sprintf('Solicitud #%d de %s eliminada', $id, $nombre ?: 'sin nombre');
        $mensaje .= $r['archivos'] > 0
            ? sprintf(', junto con %d archivo(s) adjunto(s).', $r['archivos'])
            : '.';
        if ($r['no_borrados']) {
            $mensaje .= sprintf(' No se pudieron borrar del disco %d archivo(s).', count($r['no_borrados']));
        }

        $this->redirigir($volver, 'success', $mensaje);
    }

    /**
     * Guarda el aviso en sesión y sale.
     *
     * La clave es $_SESSION['flash'][tipo], que es la que lee el layout
     * del panel; con $_SESSION['success'] el mensaje no se vería.
     */
    private function redirigir(string $url, string $tipo, string $mensaje): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash'][$tipo] = $mensaje;
        header('Location: ' . $url);
        exit;
    }
}
