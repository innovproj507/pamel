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

    // ─────────────────────────────────────────────────────────────────
    //  Catálogo de reglas
    //
    //  Cada texto se guarda una vez en requirement_rules. En la ficha del
    //  curso se marcan las que aplican y se COPIAN a course_requirements,
    //  que sigue siendo lo que lee el formulario de admisión.
    //
    //  La copia es deliberada: corregir una regla no reescribe sola lo ya
    //  publicado. desactualizadas() dice qué cursos se quedaron atrás y
    //  propagarRegla() los pone al día cuando se decida.
    // ─────────────────────────────────────────────────────────────────

    /**
     * Catálogo completo. Con $mode devuelve sólo las reglas que se ofrecen
     * en esa modalidad (las de alcance 'any' salen siempre).
     */
    public function reglas(?string $mode = null): array
    {
        $sql    = "SELECT r.*,
                          (SELECT COUNT(DISTINCT cr.product_id) FROM course_requirements cr
                            WHERE cr.rule_id = r.id) AS cursos
                   FROM requirement_rules r";
        $params = [];

        if ($mode !== null && in_array($mode, self::MODOS, true)) {
            $sql     .= " WHERE r.scope IN ('any', ?)";
            $params[] = $mode;
        }

        return $this->db->fetchAll($sql . ' ORDER BY r.is_default DESC, r.order_num, r.id', $params);
    }

    /**
     * Reglas marcadas hoy para un curso y modalidad.
     *
     * @return array<int,bool> Indexado por id de regla.
     */
    public function reglasMarcadas(int $productId, string $mode): array
    {
        $filas = $this->db->fetchAll(
            "SELECT DISTINCT rule_id FROM {$this->table}
              WHERE product_id = ? AND mode = ? AND rule_id IS NOT NULL",
            [$productId, $mode]
        );

        return array_fill_keys(array_map('intval', array_column($filas, 'rule_id')), true);
    }

    /**
     * Líneas escritas a mano para ese curso, las que no vienen del catálogo.
     */
    public function sueltasDeProducto(int $productId, string $mode): array
    {
        return $this->db->fetchAll(
            "SELECT label, requires_file FROM {$this->table}
              WHERE product_id = ? AND mode = ? AND rule_id IS NULL
              ORDER BY order_num, id",
            [$productId, $mode]
        );
    }

    /**
     * Reescribe los requisitos de un curso y modalidad a partir de las
     * reglas marcadas más las líneas sueltas.
     *
     * @param int[]   $ruleIds Ids de reglas marcadas, en el orden deseado.
     * @param array[] $sueltas Cada una ['label' => string, 'requires_file' => int].
     * @return int|false Requisitos escritos, o false si falló.
     */
    public function aplicarReglas(int $productId, string $mode, array $ruleIds, array $sueltas = [])
    {
        $lista = [];

        if ($ruleIds) {
            $ruleIds = array_values(array_unique(array_map('intval', $ruleIds)));
            $marcas  = implode(',', array_fill(0, count($ruleIds), '?'));
            $reglas  = $this->db->fetchAll(
                "SELECT id, label, requires_file FROM requirement_rules WHERE id IN ({$marcas})",
                $ruleIds
            );

            // Respetar el orden en que llegaron las casillas
            $porId = [];
            foreach ($reglas as $r) {
                $porId[(int) $r['id']] = $r;
            }
            foreach ($ruleIds as $id) {
                if (!isset($porId[$id])) {
                    continue;
                }
                $lista[] = [
                    'rule_id'       => $id,
                    'label'         => $porId[$id]['label'],
                    'requires_file' => (int) $porId[$id]['requires_file'],
                ];
            }
        }

        foreach ($sueltas as $s) {
            $label = trim((string) ($s['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $lista[] = [
                'rule_id'       => 0,
                'label'         => $label,
                'requires_file' => (int) ($s['requires_file'] ?? 0),
            ];
        }

        return $this->replaceForProduct($productId, $mode, $lista);
    }

    /**
     * Cursos que copiaron una versión del texto distinta a la que hoy
     * tiene la regla, agrupados por regla.
     */
    public function desactualizadas(): array
    {
        return $this->db->fetchAll(
            "SELECT r.id, r.label, COUNT(DISTINCT cr.product_id) AS cursos
               FROM requirement_rules r
               JOIN course_requirements cr ON cr.rule_id = r.id
              WHERE cr.label COLLATE utf8mb4_unicode_ci <> r.label COLLATE utf8mb4_unicode_ci
              GROUP BY r.id, r.label
              ORDER BY cursos DESC"
        );
    }

    /**
     * Copia el texto actual de una regla a todos los cursos que la usan.
     *
     * @return int|false Requisitos actualizados.
     */
    public function propagarRegla(int $ruleId)
    {
        $regla = $this->db->fetchOne(
            "SELECT label, requires_file FROM requirement_rules WHERE id = ?",
            [$ruleId]
        );
        if (!$regla) {
            return false;
        }

        $st = $this->db->query(
            "UPDATE {$this->table} SET label = ?, requires_file = ? WHERE rule_id = ?",
            [$regla['label'], (int) $regla['requires_file'], $ruleId]
        );

        return $st === false ? false : $st->rowCount();
    }

    /** Alta o edición de una regla del catálogo. */
    public function guardarRegla(array $datos, ?int $id = null)
    {
        $label = trim((string) ($datos['label'] ?? ''));
        if ($label === '') {
            return false;
        }

        $fila = [
            'label'         => $label,
            'requires_file' => !empty($datos['requires_file']) ? 1 : 0,
            'scope'         => in_array($datos['scope'] ?? '', ['any', 'new', 'refresher'], true)
                                    ? $datos['scope'] : 'any',
            'is_default'    => !empty($datos['is_default']) ? 1 : 0,
            'order_num'     => (int) ($datos['order_num'] ?? 0),
        ];

        return $id
            ? $this->db->update('requirement_rules', $fila, 'id = ?', [$id])
            : $this->db->insert('requirement_rules', $fila);
    }

    /**
     * Borra una regla del catálogo. Lo ya publicado no se toca: esas líneas
     * quedan como sueltas, porque son requisitos que los cursos siguen
     * exigiendo aunque la regla deje de ofrecerse.
     */
    public function borrarRegla(int $id): bool
    {
        $this->db->query("UPDATE {$this->table} SET rule_id = NULL WHERE rule_id = ?", [$id]);

        return $this->db->delete('requirement_rules', 'id = ?', [$id]);
    }

    /**
     * Todos los cursos activos con cuántos requisitos tienen en cada
     * modalidad. Es la lista que faltaba: un curso sin documento Word no
     * aparecía por ninguna parte.
     */
    public function cursosConRequisitos(): array
    {
        return $this->db->fetchAll(
            "SELECT p.id, p.name, p.course_code,
                    SUM(CASE WHEN cr.mode = 'new'       THEN 1 ELSE 0 END) AS n_nuevo,
                    SUM(CASE WHEN cr.mode = 'refresher' THEN 1 ELSE 0 END) AS n_refresco
               FROM products p
               LEFT JOIN course_requirements cr ON cr.product_id = p.id
              WHERE p.status = 'active'
              GROUP BY p.id, p.name, p.course_code
              -- Primero los cursos sin ningún requisito, que son los que hay
              -- que atender. Se repite el agregado en vez de usar los alias:
              -- MariaDB no admite referirse a ellos dentro de una expresión
              -- del ORDER BY, y la consulta entera fallaba en producción.
              ORDER BY COUNT(cr.id) = 0 DESC, p.name ASC"
        );
    }

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
                'id'      => (int) $f['id'],
                'label'   => $f['label'],
                'adjunto' => (int) $f['requires_file'] === 1,
                // Con varios cursos elegidos, el formulario agrupa por esto
                // para no pedir dos veces el mismo documento.
                'grupo'   => self::grupoRequisito((string) $f['label']),
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
            if ((int) $r['requires_file'] === 1) {
                $out[(int) $r['id']] = $r['label'];
            }
        }
        return $out;
    }

    /**
     * Clave con la que se decide que dos requisitos son el mismo.
     *
     * Se compara el texto, no el id: cada curso tiene su propia fila aunque
     * la etiqueta sea idéntica. «ID / PASSPORT» está repetido en 64 cursos,
     * así que sin esto un alumno con cuatro cursos subiría su pasaporte
     * cuatro veces.
     *
     * Se normaliza a minúsculas, con los espacios colapsados y sin signos
     * de puntuación al final, que es donde están casi todas las diferencias
     * de tecleo entre un curso y otro.
     */
    public static function claveRequisito(string $label): string
    {
        $k = preg_replace('/\s+/u', ' ', trim($label));

        return rtrim(mb_strtolower($k, 'UTF-8'), " .;:·-");
    }

    /**
     * Identificador corto del grupo, para usarlo como nombre de campo.
     *
     * La clave es el texto del requisito y no vale como name de un input.
     * El formulario recibe este identificador ya calculado desde PHP y
     * agrupa por él; el servidor lo vuelve a calcular igual. Así la regla
     * de qué cuenta como «el mismo requisito» vive en un solo sitio.
     */
    public static function grupoRequisito(string $label): string
    {
        return substr(sha1(self::claveRequisito($label)), 0, 12);
    }

    /**
     * Requisitos de varios cursos a la vez, unificados por etiqueta.
     *
     * Cada par recibido es ['product_id' => int, 'mode' => 'new'|'refresher'].
     * El resultado trae un renglón por etiqueta distinta, con todos los ids
     * de requisito que cubre y los cursos que lo piden, para poder decirle
     * al solicitante por qué se lo estamos pidiendo.
     *
     * Es la misma función que alimenta el formulario y la que valida el
     * envío: si divergieran, el servidor pediría archivos que la página
     * nunca llegó a mostrar.
     *
     * @param array<array{product_id:int,mode:string,course_name?:string}> $pares
     * @return array<array{clave:string,grupo:string,label:string,adjunto:bool,
     *                     requirement_ids:int[],cursos:string[]}>
     */
    public function unificadosParaCursos(array $pares): array
    {
        $out = [];

        foreach ($pares as $par) {
            $pid  = (int) ($par['product_id'] ?? 0);
            $modo = in_array($par['mode'] ?? 'new', self::MODOS, true) ? $par['mode'] : 'new';
            if ($pid <= 0) {
                continue;
            }

            $nombre = $par['course_name'] ?? '';

            foreach ($this->forProduct($pid, $modo) as $r) {
                $clave = self::claveRequisito((string) $r['label']);
                if ($clave === '') {
                    continue;
                }

                if (!isset($out[$clave])) {
                    $out[$clave] = [
                        'clave'           => $clave,
                        'grupo'           => self::grupoRequisito((string) $r['label']),
                        'label'           => $r['label'],
                        'adjunto'         => (int) $r['requires_file'] === 1,
                        'requirement_ids' => [],
                        'cursos'          => [],
                    ];
                }

                // Si un curso lo pide con documento y otro no, se pide el
                // documento: es el criterio más exigente de los dos.
                if ((int) $r['requires_file'] === 1) {
                    $out[$clave]['adjunto'] = true;
                }

                $out[$clave]['requirement_ids'][] = (int) $r['id'];
                if ($nombre !== '' && !in_array($nombre, $out[$clave]['cursos'], true)) {
                    $out[$clave]['cursos'][] = $nombre;
                }
            }
        }

        // Primero lo que hay que adjuntar: es lo que da trabajo.
        $lista = array_values($out);
        usort($lista, static fn($a, $b) => ($b['adjunto'] <=> $a['adjunto'])
                                        ?: strcmp($a['label'], $b['label']));

        return $lista;
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
                "INSERT INTO {$this->table} (product_id, mode, rule_id, label, requires_file, order_num)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            $orden = 0;
            foreach ($lista as $item) {
                $label = trim((string) ($item['label'] ?? ''));
                if ($label === '') {
                    continue;
                }
                $ruleId = (int) ($item['rule_id'] ?? 0);
                $insert->execute([
                    $productId,
                    $mode,
                    $ruleId > 0 ? $ruleId : null,
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
