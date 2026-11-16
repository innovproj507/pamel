<?php

namespace Plugins\Ecommerce\Controllers;

use Core\Auth;
use Core\Controller;
use Core\Slug;
use Plugins\Ecommerce\Models\Branch;
use Plugins\Ecommerce\Models\Category;
use Plugins\Ecommerce\Models\CourseSheet;
use Plugins\Ecommerce\Models\Modality;

/**
 * Alta de productos (cursos): se da de alta el curso una vez y se marcan los
 * países en que se ofrece. Cada país es una fila de `products` (la oferta)
 * con su precio, horas, modalidad y estado.
 */
class AdminCourseSheetController extends Controller
{
    private CourseSheet $sheets;

    public function __construct()
    {
        parent::__construct();
        $this->sheets = new CourseSheet();
    }

    public function create()
    {
        Auth::getInstance()->requireCan('shop.manage', '/manager/login');
        $this->renderForm(null, []);
    }

    public function edit($id)
    {
        Auth::getInstance()->requireCan('shop.manage', '/manager/login');

        $sheet = $this->sheetOfProduct((int) $id);
        if (!$sheet) {
            $this->flash('error', 'El producto no existe.');
            $this->redirect('/manager/products');
        }
        $this->renderForm($sheet, $this->sheets->offersByBranch((int) $sheet['id']), [], (int) $id);
    }

    public function store()
    {
        Auth::getInstance()->requireCan('shop.manage', '/manager/login');
        $this->requireCsrf();
        $this->persist(null);
    }

    public function update($id)
    {
        Auth::getInstance()->requireCan('shop.manage', '/manager/login');
        $this->requireCsrf();

        $sheet = $this->sheetOfProduct((int) $id);
        if (!$sheet) {
            $this->redirect('/manager/products');
        }
        $this->persist($sheet, (int) $id);
    }

    /**
     * Ficha del curso al que pertenece un producto. Un producto antiguo que
     * aún no la tenga estrena una a partir de sus propios datos.
     */
    private function sheetOfProduct(int $productId): ?array
    {
        $p = $this->db->fetchOne('SELECT * FROM products WHERE id = ?', [$productId]);
        if (!$p) {
            return null;
        }
        $cid = (int) ($p['course_id'] ?? 0);
        if ($cid === 0) {
            $nombre = trim($p['name']);
            $ficha  = $this->sheets->findByName($nombre);
            $cid    = $ficha ? (int) $ficha['id'] : (int) $this->sheets->create([
                'name' => $nombre, 'course_code' => $p['course_code'] ?: null,
                'imo_model_course_no' => $p['imo_model_course_no'] ?: null,
                'category_id' => $p['category_id'], 'description' => $p['description'],
                'image' => $p['image'] ?: null,
            ]);
            $this->db->update('products', ['course_id' => $cid], 'id = :id', ['id' => $productId]);
        }
        return $this->sheets->find($cid);
    }

    // ── Guardado ─────────────────────────────────────────────────────

    private function persist(?array $sheet, int $productId = 0): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $paises = is_array($_POST['paises'] ?? null) ? $_POST['paises'] : [];

        // Países marcados, con sus datos ya limpios.
        $ofrecidos = [];
        foreach ((new Branch())->all() as $branch) {
            $p = $paises[$branch['id']] ?? null;
            if (!is_array($p) || empty($p['ofrece'])) {
                continue;
            }
            $ofrecidos[(int) $branch['id']] = [
                'branch'        => $branch,
                'price'         => $p['price'] ?? '',
                'renewal_price' => $p['renewal_price'] ?? '',
                'duration'      => $p['duration_hours'] ?? '',
                'modality_id'   => (int) ($p['modality_id'] ?? 0),
                'stock'         => $p['stock'] ?? '',
                'status'        => ($p['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active',
                'description'   => trim((string) ($p['description'] ?? '')),
            ];
        }

        $errores = [];
        if ($name === '') {
            $errores[] = 'El nombre del curso es obligatorio.';
        } elseif ($this->sheets->findByName($name, (int) ($sheet['id'] ?? 0))) {
            $errores[] = 'Ya existe un producto con ese nombre. Para añadirle otro país, edítalo y marca el país.';
        }
        if (!$ofrecidos) {
            $errores[] = 'Marca al menos un país en el que se ofrece el curso.';
        }
        foreach ($ofrecidos as $o) {
            $pais = $o['branch']['name'];
            if (!is_numeric($o['price']) || (float) $o['price'] < 0) {
                $errores[] = "{$pais}: indica el precio.";
            }
            if ($o['modality_id'] <= 0) {
                $errores[] = "{$pais}: elige la modalidad.";
            }
        }

        if ($errores) {
            // Se vuelve a pintar el formulario con lo que escribió.
            $viejo = [
                'id'                  => $sheet['id'] ?? null,
                'name'                => $name,
                'course_code'         => trim((string) ($_POST['course_code'] ?? '')),
                'imo_model_course_no' => trim((string) ($_POST['imo_model_course_no'] ?? '')),
                'category_id'         => (int) ($_POST['category_id'] ?? 0) ?: null,
                'description'         => (string) ($_POST['description'] ?? ''),
                'image'               => $sheet['image'] ?? ($_POST['current_image'] ?? ''),
            ];
            $ofertas = [];
            foreach ($ofrecidos as $branchId => $o) {
                $ofertas[$branchId] = [
                    'status' => $o['status'], 'price' => $o['price'], 'renewal_price' => $o['renewal_price'],
                    'duration_hours' => $o['duration'], 'modality_id' => $o['modality_id'],
                    'stock' => $o['stock'], 'description' => $o['description'],
                    'description_custom' => $o['description'] !== '' ? 1 : 0,
                ];
            }
            $this->renderForm($viejo, $ofertas, $errores, $productId);
            return;
        }

        $imagen = $this->productos()->handleImageUpload($_POST['image_url'] ?? '', $_POST['current_image'] ?? '');

        $ficha = [
            'name'                => $name,
            'course_code'         => trim((string) ($_POST['course_code'] ?? '')) ?: null,
            'imo_model_course_no' => trim((string) ($_POST['imo_model_course_no'] ?? '')) ?: null,
            'category_id'         => (int) ($_POST['category_id'] ?? 0) ?: null,
            'description'         => (string) ($_POST['description'] ?? ''),
            'image'               => $imagen !== '' ? $imagen : null,
        ];

        if ($sheet) {
            $courseId = (int) $sheet['id'];
            $this->sheets->update($courseId, $ficha);
        } else {
            $courseId = (int) $this->sheets->create($ficha);
            if ($courseId <= 0) {
                $this->flash('error', 'No se pudo crear el producto.');
                $this->redirect('/manager/products/create');
            }
        }

        $imagenCambio = !$sheet || (string) ($sheet['image'] ?? '') !== (string) ($ficha['image'] ?? '');
        $existentes   = $this->sheets->offersByBranch($courseId);

        foreach ($ofrecidos as $branchId => $o) {
            $this->guardarOferta($courseId, $ficha, $o, $existentes[$branchId] ?? null, $imagenCambio);
        }

        // Un país desmarcado no borra su oferta: hay pedidos y carritos que
        // la citan. Se desactiva y desaparece de la tienda.
        $desactivadas = 0;
        foreach ($existentes as $branchId => $oferta) {
            if (!isset($ofrecidos[$branchId]) && $oferta['status'] === 'active') {
                $this->db->update('products', ['status' => 'inactive'], 'id = :id', ['id' => (int) $oferta['id']]);
                $desactivadas++;
            }
        }

        $msg = $sheet ? 'Producto guardado.' : 'Producto creado.';
        if ($desactivadas) {
            $msg .= " {$desactivadas} oferta(s) de países desmarcados quedaron inactivas.";
        }
        $this->flash('success', $msg);
        $this->redirect('/manager/products');
    }

    /** Crea o actualiza la oferta (fila de products) de un país. */
    private function guardarOferta(int $courseId, array $ficha, array $o, ?array $existente, bool $imagenCambio): void
    {
        // Descripción propia sólo si el texto escrito difiere de la común.
        $propia = $o['description'] !== '' && $o['description'] !== trim((string) $ficha['description']);

        $datos = [
            'course_id'           => $courseId,
            'name'                => $ficha['name'],
            'course_code'         => $ficha['course_code'],
            'imo_model_course_no' => $ficha['imo_model_course_no'],
            'category_id'         => $ficha['category_id'],
            'description'         => $propia ? $o['description'] : $ficha['description'],
            'description_custom'  => $propia ? 1 : 0,
            'price'               => (float) $o['price'],
            'renewal_price'       => is_numeric($o['renewal_price']) ? (float) $o['renewal_price'] : 0,
            'duration_hours'      => is_numeric($o['duration']) ? (float) $o['duration'] : 0,
            'modality_id'         => $o['modality_id'],
            'stock'               => is_numeric($o['stock']) ? (int) $o['stock'] : 99,
            'branch_id'           => (int) $o['branch']['id'],
            'status'              => $o['status'],
        ];

        if ($existente) {
            if ($imagenCambio || empty($existente['image'])) {
                $datos['image'] = $ficha['image'];
            }
            $this->db->update('products', $datos, 'id = :id', ['id' => (int) $existente['id']]);
            // Mantiene al día el precio del curso del LMS enlazado.
            $this->db->update('lms_courses', ['price' => $datos['price']], 'product_id = :pid', ['pid' => (int) $existente['id']]);
            $productId = (int) $existente['id'];
        } else {
            $datos['image'] = $ficha['image'];
            $datos['slug']  = $this->slugLibre($ficha['name'], (string) $o['branch']['slug']);
            $productId = (int) $this->db->insert('products', $datos);
        }

        if ($productId > 0) {
            // No-op si ya tiene curso en el LMS; si no, crea el borrador.
            $this->productos()->autoCreateLmsCourse(
                $productId, $datos['name'], (string) $datos['description'],
                (float) $datos['price'], $datos['course_code'], (int) $datos['branch_id']
            );
        }
    }

    /** Slug único: primero el del nombre, luego con el país, luego numerado. */
    private function slugLibre(string $nombre, string $pais): string
    {
        $base = Slug::make($nombre);
        $prod = new \Plugins\Ecommerce\Models\Product();
        foreach ([$base, $base . '-' . $pais] as $candidato) {
            if (!$prod->findBySlug($candidato)) {
                return $candidato;
            }
        }
        $n = 2;
        while ($prod->findBySlug("{$base}-{$pais}-{$n}")) {
            $n++;
        }
        return "{$base}-{$pais}-{$n}";
    }

    private function productos(): AdminProductController
    {
        return new AdminProductController();
    }

    private function renderForm(?array $sheet, array $offers, array $errors = [], int $productId = 0): void
    {
        $this->view->render('plugins/ecommerce/views/admin/product-form', [
            'title'      => $sheet && !empty($sheet['id']) ? 'Editar producto' : 'Nuevo producto',
            'productId'  => $productId,
            'sheet'      => $sheet,
            'offers'     => $offers,
            'errors'     => $errors,
            'branches'   => (new Branch())->all(),
            'categories' => (new Category())->all(),
            'modalities' => (new Modality())->all(),
        ], 'admin/views/layout');
    }
}
