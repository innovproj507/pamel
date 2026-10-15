<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\View;
use Core\Auth;
use Core\WordGenerator;
use Plugins\Elearning\Models\Progress;

/**
 * Seguimiento académico: avance de cada estudiante, exámenes realizados y
 * descarga del acta de un examen.
 *
 * Todo el progreso se calcula desde lms_lesson_progress y
 * lms_quiz_attempts; la columna progress_percentage de lms_enrollments
 * nunca se ha escrito y mostraba cero para todos.
 */
class AdminTrackingController extends Controller
{
    private Progress $progreso;

    public function __construct()
    {
        parent::__construct();
        $this->progreso = new Progress();
    }

    /** Panel de seguimiento: indicadores y listado de matrículas. */
    public function index(): void
    {
        Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');

        $filtros = [
            'course_id' => (int) ($_GET['curso'] ?? 0) ?: null,
            'buscar'    => trim((string) ($_GET['buscar'] ?? '')) ?: null,
            'estado'    => in_array($_GET['estado'] ?? '', ['sin_empezar', 'en_curso', 'completado'], true)
                            ? $_GET['estado'] : null,
        ];
        $filtros = array_filter($filtros, fn($v) => $v !== null);

        // El resumen se calcula sin el filtro de estado: si no, las
        // tarjetas mostrarían sólo el estado ya seleccionado.
        $paraResumen = $filtros;
        unset($paraResumen['estado']);

        (new View())->render('admin/views/lms/tracking/index', [
            'title'   => 'Seguimiento de estudiantes',
            'filas'   => $this->progreso->matriculas($filtros),
            'resumen' => $this->progreso->resumen($paraResumen),
            'cursos'  => $this->db->fetchAll(
                "SELECT c.id, c.title FROM lms_courses c
                  WHERE EXISTS (SELECT 1 FROM lms_enrollments e WHERE e.course_id = c.id)
                  ORDER BY c.title ASC"
            ),
            'filtros' => $filtros,
        ], 'admin/views/layout');
    }

    /** Ficha de un estudiante: sus cursos y sus exámenes. */
    public function student($id): void
    {
        Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');

        $id = (int) $id;
        $estudiante = $this->db->fetchOne(
            'SELECT id, name, email, created_at FROM users WHERE id = ?',
            [$id]
        );
        if (!$estudiante) {
            $this->flash('error', 'El estudiante no existe.');
            $this->redirect('/manager/lms/seguimiento');
        }

        (new View())->render('admin/views/lms/tracking/student', [
            'title'      => 'Seguimiento: ' . $estudiante['name'],
            'estudiante' => $estudiante,
            'cursos'     => $this->progreso->matriculas(['student_id' => $id]),
            'examenes'   => $this->progreso->examenesDeEstudiante($id),
        ], 'admin/views/layout');
    }

    /**
     * Descarga el acta de un examen en Word.
     *
     * Admite dos formas de autorización: una sesión con permiso en el
     * panel, o un enlace firmado, que es como llega el estudiante desde el
     * portal —otro dominio, donde no hay sesión de administrador.
     */
    public function downloadAttempt($id): void
    {
        $id = (int) $id;

        if (!$this->autorizadoParaActa($id)) {
            http_response_code(403);
            exit('No autorizado para descargar este documento.');
        }

        $intento = $this->progreso->intentoCompleto($id);
        if (!$intento) {
            http_response_code(404);
            exit('Intento no encontrado.');
        }

        try {
            $ruta = WordGenerator::generateQuizAttemptDocument($intento);
        } catch (\Throwable $e) {
            error_log('Acta de examen ' . $id . ': ' . $e->getMessage());
            http_response_code(500);
            exit('No se pudo generar el documento. Revisa el log del servidor.');
        }

        $nombre = sprintf(
            'Acta_%s_%s.docx',
            preg_replace('/[^A-Za-z0-9]+/', '-', (string) $intento['student_name']),
            date('Y-m-d', strtotime((string) ($intento['completed_at'] ?: 'now')))
        );

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $nombre . '"');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        @unlink($ruta);
        exit;
    }

    /** Firma de un enlace de descarga, para el portal del estudiante. */
    public static function firmaActa(int $attemptId): string
    {
        return substr(hash_hmac('sha256', 'acta:' . $attemptId, \Core\Config::get('app.secret', '')), 0, 32);
    }

    /** ¿Puede esta petición descargar el acta? */
    private function autorizadoParaActa(int $attemptId): bool
    {
        $auth = Auth::getInstance();
        if ($auth->check() && $auth->can('lms.courses.manage')) {
            return true;
        }

        $firma = (string) ($_GET['f'] ?? '');

        return $firma !== '' && hash_equals(self::firmaActa($attemptId), $firma);
    }
}
