<?php

namespace Plugins\Ecommerce\Services;

use Core\Config;
use Core\Database;

/**
 * Mantiene el curso del LMS al día con el producto de la tienda al que está ligado.
 *
 * Un curso existe dos veces: como producto (tienda) y como curso LMS (lecciones,
 * quizzes). Cada uno se editaba por separado y los datos se fueron separando.
 * Esta clase aplica reglas CONSERVADORAS: sólo rellena lo que el curso LMS
 * tiene vacío y avisa del resto, sin pisar nada que alguien haya escrito:
 *
 *  - código del curso, imagen y descripción: se copian del producto SÓLO si el
 *    curso LMS no los tiene. La imagen se guarda con URL completa (como las que
 *    ya hay en el LMS), porque el portal se sirve desde otro dominio.
 *  - título, slug y precio: NO se tocan. El slug es la dirección pública del
 *    curso en el portal y cambiarlo rompería enlaces ya compartidos; el título
 *    y el precio pueden haberse corregido a propósito en un lado (p. ej. el
 *    producto dice «Skils» y el curso «Skills»). Se devuelven como avisos para
 *    revisarlos a mano.
 *
 * Llamar a sincronizar() tras guardar un producto mantiene al día los huecos.
 */
class CourseSync
{
    private Database $db;

    public function __construct(?Database $db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }

    /**
     * Qué cambiaría, sin escribir nada.
     *
     * @param int|null $productId Limita el cálculo a un producto.
     * @return array{cambios: array<int,array>, avisos: array<int,array>}
     */
    public function planificar(?int $productId = null): array
    {
        $sql = "SELECT c.id AS course_id, c.title, c.price AS lms_price, c.course_code AS lms_code,
                       c.image AS lms_image, c.description AS lms_desc,
                       p.id AS product_id, p.name, p.price AS prod_price, p.course_code AS prod_code,
                       p.image AS prod_image, p.description AS prod_desc
                FROM lms_courses c
                JOIN products p ON p.id = c.product_id";
        $params = [];
        if ($productId !== null) {
            $sql .= ' WHERE p.id = ?';
            $params[] = $productId;
        }

        $cambios = [];
        $avisos  = [];
        foreach ($this->db->fetchAll($sql, $params) as $f) {
            $nuevo = [];

            if ($this->vacio($f['lms_code']) && !$this->vacio($f['prod_code'])) {
                $nuevo['course_code'] = [$f['lms_code'], $f['prod_code']];
            }
            if ($this->vacio($f['lms_image']) && !$this->vacio($f['prod_image'])) {
                $nuevo['image'] = [$f['lms_image'], $this->urlCompleta($f['prod_image'])];
            }
            if ($this->vacio($f['lms_desc']) && !$this->vacio($f['prod_desc'])) {
                $nuevo['description'] = [$f['lms_desc'], $f['prod_desc']];
            }
            if ($nuevo) {
                $cambios[] = ['course_id' => (int) $f['course_id'], 'product_id' => (int) $f['product_id'], 'campos' => $nuevo];
            }

            if (trim((string) $f['title']) !== trim((string) $f['name'])) {
                $avisos[] = ['course_id' => (int) $f['course_id'], 'tipo' => 'titulo',
                             'producto' => $f['name'], 'curso' => $f['title']];
            }
            if ((float) $f['lms_price'] !== (float) $f['prod_price']) {
                $avisos[] = ['course_id' => (int) $f['course_id'], 'tipo' => 'precio',
                             'producto' => $f['prod_price'], 'curso' => $f['lms_price']];
            }
        }

        return ['cambios' => $cambios, 'avisos' => $avisos];
    }

    /** Escribe los cambios de un plan. Devuelve cuántos cursos se actualizaron. */
    public function aplicar(array $cambios): int
    {
        $n = 0;
        foreach ($cambios as $c) {
            $datos = array_map(static fn($par) => $par[1], $c['campos']);
            if ($datos && $this->db->update('lms_courses', $datos, 'id = :id', ['id' => $c['course_id']])) {
                $n++;
            }
        }
        return $n;
    }

    /** Atajo para tras guardar un producto: rellena los huecos de su curso. */
    public function sincronizar(int $productId): int
    {
        return $this->aplicar($this->planificar($productId)['cambios']);
    }

    private function vacio($valor): bool
    {
        return $valor === null || trim((string) $valor) === '';
    }

    private function urlCompleta(string $ruta): string
    {
        if (preg_match('#^https?://#i', $ruta)) {
            return $ruta;
        }
        return rtrim((string) Config::get('site.url', ''), '/') . '/' . ltrim($ruta, '/');
    }
}
