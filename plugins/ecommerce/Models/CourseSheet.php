<?php

namespace Plugins\Ecommerce\Models;

use Core\Model;

/**
 * Ficha única de un curso (tabla `courses`).
 *
 * La ficha guarda lo que es común a todos los países: nombre, código,
 * categoría, descripción e imagen. Cada país en que se da el curso es una
 * fila de `products` (la «oferta») con su precio, horas, modalidad, estado
 * y requisitos, enlazada por products.course_id.
 */
class CourseSheet extends Model
{
    protected $table = 'courses';

    /** Fichas con el número de ofertas y los países que las dan. */
    public function listing(string $search = '', string $status = ''): array
    {
        $where  = [];
        $params = [];

        if ($search !== '') {
            $where[]  = '(c.name LIKE ? OR c.course_code LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }
        // «Activa» = al menos una oferta activa.
        if ($status === 'active') {
            $where[] = "EXISTS (SELECT 1 FROM products p WHERE p.course_id = c.id AND p.status = 'active')";
        } elseif ($status === 'inactive') {
            $where[] = "NOT EXISTS (SELECT 1 FROM products p WHERE p.course_id = c.id AND p.status = 'active')";
        }

        $sql = "SELECT c.*, cat.name AS category_name
                  FROM {$this->table} c
                  LEFT JOIN categories cat ON cat.id = c.category_id"
             . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
             . ' ORDER BY c.name ASC';

        $fichas = $this->db->fetchAll($sql, $params);
        if (!$fichas) {
            return [];
        }

        // Todas las ofertas de una vez, en lugar de una consulta por ficha.
        $ofertas = $this->db->fetchAll(
            "SELECT p.id, p.course_id, p.status, p.price, p.branch_id,
                    b.name AS branch_name, b.slug AS branch_slug
               FROM products p
               LEFT JOIN branches b ON b.id = p.branch_id
              WHERE p.course_id IS NOT NULL
              ORDER BY b.display_order, b.name"
        );
        $porFicha = [];
        foreach ($ofertas as $o) {
            $porFicha[(int) $o['course_id']][] = $o;
        }
        foreach ($fichas as &$f) {
            $f['offers'] = $porFicha[(int) $f['id']] ?? [];
        }
        unset($f);

        return $fichas;
    }

    /** Ofertas de una ficha, indexadas por id de sucursal. */
    public function offersByBranch(int $courseId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT * FROM products WHERE course_id = ? ORDER BY id ASC",
            [$courseId]
        );
        $out = [];
        foreach ($rows as $r) {
            // Si hubiera dos de la misma sucursal, manda la más antigua.
            $out[(int) $r['branch_id']] ??= $r;
        }
        return $out;
    }

    public function findByName(string $name, int $exceptId = 0): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE name = ? AND id <> ?",
            [trim($name), $exceptId]
        ) ?: null;
    }
}
