<?php

namespace Core\Models;

use Core\Model;

/**
 * Requisitos de admisión por curso.
 *
 * Los documentos de la carpeta Requisitos/ se importan primero a
 * requirement_imports (zona de revisión) y desde ahí se publican a
 * course_requirements, que es lo que consulta el formulario público.
 */
class CourseRequirement extends Model
{
    protected $table = 'course_requirements';

    /** Modos admitidos: curso regular o refresco. */
    public const MODOS = ['new', 'refresher'];

    /**
     * Requisitos publicados de un curso, en orden.
     */
    public function forProduct(int $productId, string $mode = 'new'): array
    {
        if (!in_array($mode, self::MODOS, true)) {
            $mode = 'new';
        }

        return $this->db->fetchAll(
            "SELECT id, label, requires_file, order_num
             FROM {$this->table}
             WHERE product_id = ? AND mode = ?
             ORDER BY order_num ASC, id ASC",
            [$productId, $mode]
        );
    }

    /**
     * ¿De qué modos tiene requisitos este curso? Sirve para saber si procede
     * ofrecer la opción de refresco en el formulario.
     */
    public function modesForProduct(int $productId): array
    {
        $filas = $this->db->fetchAll(
            "SELECT DISTINCT mode FROM {$this->table} WHERE product_id = ?",
            [$productId]
        );
        return array_column($filas, 'mode');
    }

    /**
     * Resumen para el panel: cuántos cursos tienen requisitos y de qué tipo.
     */
    public function summary(): array
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT product_id) AS cursos,
                    COUNT(*)                   AS requisitos,
                    SUM(mode = 'new')          AS modo_nuevo,
                    SUM(mode = 'refresher')    AS modo_refresco
             FROM {$this->table}"
        );

        return [
            'cursos'        => (int) ($row['cursos'] ?? 0),
            'requisitos'    => (int) ($row['requisitos'] ?? 0),
            'modo_nuevo'    => (int) ($row['modo_nuevo'] ?? 0),
            'modo_refresco' => (int) ($row['modo_refresco'] ?? 0),
        ];
    }

    /**
     * Requisitos ya cubiertos por los campos fijos del formulario.
     * No se vuelven a pedir como adjunto aparte.
     */
    private const YA_EN_FORMULARIO = [
        'id / passport',
        'good health, certified by means of a medical certificate',
    ];

    /** ¿Este requisito lo cubre ya un campo fijo del formulario? */
    public static function cubiertoPorCampoFijo(string $label): bool
    {
        $l = mb_strtolower(trim($label), 'UTF-8');
        foreach (self::YA_EN_FORMULARIO as $fijo) {
            if ($l === $fijo || str_starts_with($l, $fijo)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Todos los requisitos agrupados por curso y modo, para incrustarlos en
     * el formulario de admisión.
     *
     * @return array<int, array{new: array, refresher: array}>
     */
    public function allForForm(): array
    {
        $filas = $this->db->fetchAll(
            "SELECT r.id, r.product_id, r.mode, r.label, r.requires_file
             FROM {$this->table} r
             JOIN products p ON p.id = r.product_id AND p.status = 'active'
             ORDER BY r.product_id, r.mode, r.order_num ASC, r.id ASC"
        );

        $out = [];
        foreach ($filas as $f) {
            $pid  = (int) $f['product_id'];
            $modo = $f['mode'];
            $out[$pid][$modo][] = [
                'id'       => (int) $f['id'],
                'label'    => $f['label'],
                'adjunto'  => (int) $f['requires_file'] === 1
                              && !self::cubiertoPorCampoFijo($f['label']),
                'cubierto' => self::cubiertoPorCampoFijo($f['label']),
            ];
        }

        // Normaliza para que siempre existan ambas claves
        foreach ($out as $pid => $modos) {
            $out[$pid] = [
                'new'       => $modos['new'] ?? [],
                'refresher' => $modos['refresher'] ?? [],
            ];
        }

        return $out;
    }

    /**
     * Requisitos que exigen adjunto para un curso y modo concretos.
     * Lo usa el servidor para validar lo que llega del formulario.
     *
     * @return array<int, string> id del requisito => etiqueta
     */
    public function adjuntosRequeridos(int $productId, string $mode): array
    {
        $out = [];
        foreach ($this->forProduct($productId, $mode) as $r) {
            if ((int) $r['requires_file'] === 1 && !self::cubiertoPorCampoFijo($r['label'])) {
                $out[(int) $r['id']] = $r['label'];
            }
        }
        return $out;
    }

    /**
     * Reemplaza por completo los requisitos de un curso y modo.
     * Es lo que usa el editor del panel.
     *
     * @param array<array{label:string,requires_file:int}> $lista
     * @return int|false Requisitos guardados, o false si falló.
     */
    public function replaceForProduct(int $productId, string $mode, array $lista)
    {
        if (!in_array($mode, self::MODOS, true)) {
            return false;
        }

        $existe = $this->db->fetchOne("SELECT id FROM products WHERE id = ?", [$productId]);
        if (!$existe) {
            return false;
        }

        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $pdo->prepare("DELETE FROM {$this->table} WHERE product_id = ? AND mode = ?")
                ->execute([$productId, $mode]);

            $insert = $pdo->prepare(
                "INSERT INTO {$this->table} (product_id, mode, label, requires_file, order_num)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $orden = 0;
            foreach ($lista as $item) {
                $label = trim((string) ($item['label'] ?? ''));
                if ($label === '') {
                    continue;
                }
                $insert->execute([
                    $productId,
                    $mode,
                    $label,
                    (int) ($item['requires_file'] ?? 0),
                    ++$orden,
                ]);
            }

            $pdo->commit();
            return $orden;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log('CourseRequirement::replaceForProduct: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Publica un documento importado: vuelca su lista a course_requirements,
     * reemplazando lo que hubiera para ese curso y modo.
     *
     * @return int|false Requisitos publicados, o false si falló.
     */
    public function publishImport(int $importId)
    {
        $doc = $this->db->fetchOne(
            "SELECT folder, product_id, requirements FROM requirement_imports WHERE id = ?",
            [$importId]
        );

        if (!$doc || empty($doc['product_id'])) {
            return false;
        }

        $modo  = $doc['folder'] === 'R-RF' ? 'refresher' : 'new';
        $lista = json_decode((string) $doc['requirements'], true);

        if (!is_array($lista) || !$lista) {
            return false;
        }

        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();

        try {
            $pdo->prepare("DELETE FROM {$this->table} WHERE product_id = ? AND mode = ?")
                ->execute([(int) $doc['product_id'], $modo]);

            $insert = $pdo->prepare(
                "INSERT INTO {$this->table} (product_id, mode, label, requires_file, order_num)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $orden = 0;
            foreach ($lista as $item) {
                $label = trim((string) ($item['label'] ?? ''));
                if ($label === '') {
                    continue;
                }
                $insert->execute([
                    (int) $doc['product_id'],
                    $modo,
                    $label,
                    (int) ($item['requires_file'] ?? 0),
                    ++$orden,
                ]);
            }

            $pdo->prepare("UPDATE requirement_imports SET status = 'mapped' WHERE id = ?")
                ->execute([$importId]);

            $pdo->commit();
            return $orden;
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log('CourseRequirement::publishImport: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Asigna un curso a un documento importado que estaba sin mapear.
     */
    public function assignImport(int $importId, int $productId): bool
    {
        $existe = $this->db->fetchOne("SELECT id FROM products WHERE id = ?", [$productId]);
        if (!$existe) {
            return false;
        }

        return $this->db->update(
            'requirement_imports',
            ['product_id' => $productId, 'status' => 'mapped'],
            'id = :id',
            ['id' => $importId]
        ) !== false;
    }

    /**
     * Descarta un documento importado.
     */
    public function ignoreImport(int $importId): bool
    {
        return $this->db->update(
            'requirement_imports',
            ['status' => 'ignored'],
            'id = :id',
            ['id' => $importId]
        ) !== false;
    }

    /**
     * Documentos importados, con el nombre del curso asignado.
     */
    public function imports(string $status = 'pending', int $limit = 20, int $offset = 0): array
    {
        $where  = '1=1';
        $params = [];
        if (in_array($status, ['pending', 'mapped', 'ignored'], true)) {
            $where    = 'i.status = ?';
            $params[] = $status;
        }

        $limit  = max(1, $limit);
        $offset = max(0, $offset);

        return $this->db->fetchAll(
            "SELECT i.*, p.name AS product_name
             FROM requirement_imports i
             LEFT JOIN products p ON p.id = i.product_id
             WHERE {$where}
             ORDER BY i.folder ASC, i.doc_title ASC
             LIMIT {$limit} OFFSET {$offset}",
            $params
        );
    }

    public function countImports(string $status = 'pending'): int
    {
        if (!in_array($status, ['pending', 'mapped', 'ignored'], true)) {
            $row = $this->db->fetchOne("SELECT COUNT(*) AS c FROM requirement_imports");
        } else {
            $row = $this->db->fetchOne(
                "SELECT COUNT(*) AS c FROM requirement_imports WHERE status = ?",
                [$status]
            );
        }
        return (int) ($row['c'] ?? 0);
    }

    /** Conteo por estado, para las pestañas del panel. */
    public function importCounts(): array
    {
        $filas = $this->db->fetchAll(
            "SELECT status, COUNT(*) AS c FROM requirement_imports GROUP BY status"
        );
        $out = ['pending' => 0, 'mapped' => 0, 'ignored' => 0];
        foreach ($filas as $f) {
            $out[$f['status']] = (int) $f['c'];
        }
        return $out;
    }
}
