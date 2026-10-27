<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\View;
use Core\Security;
use Core\Models\CourseRequirement;

/**
 * Requisitos de admisión por curso.
 *
 * Dos pantallas: la bandeja de documentos importados de la carpeta Requisitos/
 * (asignar curso, publicar o descartar) y el editor de los requisitos ya
 * publicados de cada curso.
 */
class AdminRequirementController extends Controller
{
    private CourseRequirement $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new CourseRequirement();
    }

    /** Bandeja de documentos importados. */
    public function index(): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');

        $estado  = $_GET['estado'] ?? 'pending';
        if (!in_array($estado, ['pending', 'mapped', 'ignored'], true)) {
            $estado = 'pending';
        }

        // Sin paginar: son poco más de un centenar de documentos en total y
        // la pantalla los filtra al escribir. Paginar obligaba a bajar hasta
        // el final para buscar un curso que podía estar en cualquier página.
        $total = $this->model->countImports($estado);
        $docs  = $this->model->imports($estado, 500, 0);
        foreach ($docs as &$d) {
            $d['lista'] = json_decode((string) $d['requirements'], true) ?: [];
        }
        unset($d);

        (new View())->render('admin/views/requirements/index', [
            'title'   => 'Requisitos de admisión',
            'docs'    => $docs,
            'estado'  => $estado,
            'conteos' => $this->model->importCounts(),
            'resumen' => $this->model->summary(),
            'cursos'  => $this->cursosDisponibles(),
            'total'   => $total,
        ], 'admin/views/layout');
    }

    /** Asigna un curso a un documento importado. */
    public function assign($id): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');
        $this->guardPost();

        $productId = (int) ($_POST['product_id'] ?? 0);
        if ($productId <= 0) {
            $this->flash('error', 'Selecciona el curso al que pertenece el documento.');
            $this->volver();
        }

        if (!$this->model->assignImport((int) $id, $productId)) {
            $this->flash('error', 'No se pudo asignar el curso.');
            $this->volver();
        }

        // Asignar y publicar en un solo paso: es lo que se espera al elegir
        $n = $this->model->publishImport((int) $id);
        $this->flash(
            $n === false ? 'error' : 'success',
            $n === false
                ? 'El curso quedó asignado, pero no se pudieron publicar los requisitos.'
                : sprintf('Asignado y publicado: %d requisito(s).', $n)
        );

        $this->volver();
    }

    /** Vuelve a publicar un documento ya asignado. */
    public function publish($id): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');
        $this->guardPost();

        $n = $this->model->publishImport((int) $id);
        $this->flash(
            $n === false ? 'error' : 'success',
            $n === false
                ? 'No se pudo publicar. ¿Tiene un curso asignado?'
                : sprintf('Publicados %d requisito(s).', $n)
        );

        $this->volver();
    }

    /** Descarta un documento que no corresponde a ningún curso. */
    public function ignore($id): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');
        $this->guardPost();

        $this->model->ignoreImport((int) $id);
        $this->flash('success', 'Documento descartado.');
        $this->volver();
    }

    /**
     * Todos los cursos activos con sus requisitos.
     *
     * La bandeja lista documentos Word, así que un curso sin documento no
     * aparecía en ninguna parte. Esta es la entrada por curso.
     */
    public function courses(): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');

        (new View())->render('admin/views/requirements/courses', [
            'title'   => 'Requisitos por curso',
            'cursos'  => $this->model->cursosConRequisitos(),
            'conteos' => $this->model->importCounts(),
        ], 'admin/views/layout');
    }

    /** Requisitos publicados de un curso, editables. */
    public function course($productId): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');

        $productId = (int) $productId;
        $curso = $this->db->fetchOne(
            "SELECT id, name, course_code FROM products WHERE id = ?",
            [$productId]
        );
        if (!$curso) {
            $this->flash('error', 'El curso no existe.');
            $this->redirect('/manager/requisitos/cursos');
        }

        (new View())->render('admin/views/requirements/course', [
            'title'    => 'Requisitos: ' . $curso['name'],
            'curso'    => $curso,
            'nuevo'    => $this->model->forProduct($productId, 'new'),
            'refresco' => $this->model->forProduct($productId, 'refresher'),
            'reglas'   => [
                'new'       => $this->model->reglas('new'),
                'refresher' => $this->model->reglas('refresher'),
            ],
            'marcadas' => [
                'new'       => $this->model->reglasMarcadas($productId, 'new'),
                'refresher' => $this->model->reglasMarcadas($productId, 'refresher'),
            ],
            'sueltas'  => [
                'new'       => $this->model->sueltasDeProducto($productId, 'new'),
                'refresher' => $this->model->sueltasDeProducto($productId, 'refresher'),
            ],
        ], 'admin/views/layout');
    }

    /** Guarda la edición de los requisitos de un curso y modo. */
    public function save($productId): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');
        $this->guardPost();

        $productId = (int) $productId;
        $modo      = $_POST['mode'] ?? 'new';
        if (!in_array($modo, CourseRequirement::MODOS, true)) {
            $modo = 'new';
        }

        // Reglas marcadas del catálogo
        $ruleIds = array_map('intval', (array) ($_POST['rule_id'] ?? []));

        // Líneas escritas a mano para este curso
        $labels  = (array) ($_POST['label'] ?? []);
        $files   = (array) ($_POST['requires_file'] ?? []);
        $sueltas = [];
        foreach ($labels as $i => $label) {
            $label = trim((string) $label);
            if ($label === '') {
                continue;
            }
            $sueltas[] = [
                'label'         => $label,
                'requires_file' => !empty($files[$i]) ? 1 : 0,
            ];
        }

        $n = $this->model->aplicarReglas($productId, $modo, $ruleIds, $sueltas);

        $this->flash(
            $n === false ? 'error' : 'success',
            $n === false
                ? 'No se pudieron guardar los requisitos.'
                : sprintf('Guardados %d requisito(s).', $n)
        );

        $this->redirect('/manager/requisitos/curso/' . $productId);
    }

    // ─── Catálogo de reglas ───────────────────────────────────────────

    /** Catálogo de reglas reutilizables. */
    public function rules(): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');

        (new View())->render('admin/views/requirements/rules', [
            'title'           => 'Catálogo de requisitos',
            'reglas'          => $this->model->reglas(),
            'desactualizadas' => $this->model->desactualizadas(),
        ], 'admin/views/layout');
    }

    /** Alta o edición de una regla. */
    public function saveRule(): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');
        $this->guardPost();

        $id = (int) ($_POST['id'] ?? 0);
        $ok = $this->model->guardarRegla($_POST, $id > 0 ? $id : null);

        $this->flash(
            $ok === false ? 'error' : 'success',
            $ok === false
                ? 'No se pudo guardar la regla. ¿El texto está vacío o repetido?'
                : ($id > 0 ? 'Regla actualizada.' : 'Regla añadida al catálogo.')
        );

        $this->redirect('/manager/requisitos/reglas');
    }

    /** Copia el texto actual de una regla a los cursos que la usan. */
    public function propagateRule($id): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');
        $this->guardPost();

        $n = $this->model->propagarRegla((int) $id);

        $this->flash(
            $n === false ? 'error' : 'success',
            $n === false
                ? 'No se pudo actualizar.'
                : sprintf('Actualizados %d requisito(s) en los cursos que usan esta regla.', $n)
        );

        $this->redirect('/manager/requisitos/reglas');
    }

    /** Quita una regla del catálogo sin tocar lo ya publicado. */
    public function deleteRule($id): void
    {
        \Core\Auth::getInstance()->requireCan('lms.courses.manage', '/manager/login');
        $this->guardPost();

        $ok = $this->model->borrarRegla((int) $id);

        $this->flash(
            $ok ? 'success' : 'error',
            $ok
                ? 'Regla retirada del catálogo. Los requisitos ya publicados se conservan en cada curso.'
                : 'No se pudo retirar la regla.'
        );

        $this->redirect('/manager/requisitos/reglas');
    }

    // ─── Apoyo ────────────────────────────────────────────────────────

    /** Cursos del catálogo, para los desplegables. */
    private function cursosDisponibles(): array
    {
        return $this->db->fetchAll(
            "SELECT p.id, p.name, p.course_code,
                    (SELECT COUNT(*) FROM course_requirements r WHERE r.product_id = p.id) AS requisitos
             FROM products p
             WHERE p.status = 'active'
             ORDER BY p.name ASC"
        );
    }

    private function guardPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/manager/requisitos');
        }
        if (!isset($_POST['csrf_token']) || !Security::validateCsrfToken($_POST['csrf_token'])) {
            $this->flash('error', 'Token de seguridad inválido. Recarga la página.');
            $this->redirect('/manager/requisitos');
        }
    }

    /** Protegido, no privado: ahora lo declara también Core\Controller. */
    protected function flash(string $tipo, string $mensaje): void
    {
        $_SESSION['flash'][$tipo] = $mensaje;
    }

    /** Vuelve a la bandeja conservando el filtro y la página. */
    private function volver(): void
    {
        $qs = http_build_query(array_filter([
            'estado' => $_POST['volver_estado'] ?? null,
        ]));
        $this->redirect('/manager/requisitos' . ($qs ? "?{$qs}" : ''));
    }
}
