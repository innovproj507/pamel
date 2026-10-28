<?php

namespace Core\Models;

use Core\Model;

class AdmissionRequest extends Model
{
    protected $table = 'admission_requests';

    public function create($data)
    {
        return $this->db->insert($this->table, [
            'given_name' => $data['given_name'],
            'surname' => $data['surname'],
            'passport_id' => $data['passport_id'],
            'nationality' => $data['nationality'],
            'date_of_birth' => $data['date_of_birth'],
            'country_of_birth' => $data['country_of_birth'],
            'education_level' => $data['education_level'] ?? null,
            'email' => $data['email'],
            'address' => $data['address'],
            'capacity' => $data['capacity'] ?? null,
            'phone' => $data['phone'],
            'course' => $data['course'],
            'course_mode' => $data['course_mode'] ?? 'new',
            'consent_accepted' => $data['consent_accepted'] ?? 1,
            'id_file' => $data['id_file'] ?? null,
            'health_certificate_file' => $data['health_certificate_file'] ?? null
        ]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        return $this->db->fetchOne($sql, [(int)$id]);
    }

    /**
     * Guarda los cursos elegidos en una solicitud, cada uno con su modalidad.
     *
     * Una solicitud puede llevar varios cursos y cada uno va por su cuenta:
     * se puede estar renovando uno y estrenando otro.
     *
     * @param array<array{product_id:?int,course_name:string,mode:string}> $cursos
     */
    public function saveCourses(int $requestId, array $cursos): void
    {
        foreach (array_values($cursos) as $i => $c) {
            $this->db->insert('admission_request_courses', [
                'admission_request_id' => $requestId,
                'product_id'           => $c['product_id'] ?? null,
                'course_name'          => $c['course_name'],
                'mode'                 => in_array($c['mode'] ?? 'new', ['new', 'refresher'], true)
                                          ? $c['mode'] : 'new',
                'position'             => $i,
            ]);
        }
    }

    /**
     * Cursos de una solicitud, en el orden en que se eligieron.
     *
     * Las solicitudes anteriores a la tabla de cursos también salen por
     * aquí: la migración copió su curso único como primer renglón.
     *
     * @return array<array{product_id:?int,course_name:string,mode:string}>
     */
    public function getCourses(int $requestId): array
    {
        return $this->db->fetchAll(
            'SELECT product_id, course_name, mode
               FROM admission_request_courses
              WHERE admission_request_id = ?
              ORDER BY position ASC, id ASC',
            [$requestId]
        );
    }

    /**
     * Borra una solicitud con sus adjuntos, de la base y del disco.
     *
     * Sin esto quedarían dos rastros: las filas de admission_request_files,
     * que no tienen clave foránea con borrado en cascada, y los archivos
     * subidos, que nadie volvería a mirar pero siguen siendo documentos de
     * identidad ocupando sitio.
     *
     * @return array{borrada:bool,archivos:int,no_borrados:string[]}
     */
    public function deleteWithFiles(int $id): array
    {
        $solicitud = $this->getById($id);
        if (!$solicitud) {
            return ['borrada' => false, 'archivos' => 0, 'no_borrados' => []];
        }

        $adjuntos = $this->db->fetchAll(
            'SELECT file_path FROM admission_request_files WHERE admission_request_id = ?',
            [$id]
        );

        // Los dos campos antiguos guardaban la ruta en la propia solicitud.
        $rutas = array_column($adjuntos, 'file_path');
        foreach (['id_file', 'health_certificate_file'] as $campo) {
            if (!empty($solicitud[$campo])) {
                $rutas[] = $solicitud[$campo];
            }
        }

        $pdo = $this->db->getConnection();
        $pdo->beginTransaction();
        try {
            $pdo->prepare('DELETE FROM admission_request_files WHERE admission_request_id = ?')->execute([$id]);
            $pdo->prepare("DELETE FROM {$this->table} WHERE id = ?")->execute([$id]);
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            error_log('AdmissionRequest::deleteWithFiles: ' . $e->getMessage());
            return ['borrada' => false, 'archivos' => 0, 'no_borrados' => []];
        }

        // Los archivos se borran después de confirmar: si la transacción
        // fallara ya no habría forma de recuperarlos.
        $borrados = 0;
        $fallidos = [];
        foreach (array_unique(array_filter($rutas)) as $ruta) {
            $absoluta = $this->rutaAbsoluta((string) $ruta);
            if ($absoluta === null) {
                $fallidos[] = (string) $ruta;
                continue;
            }
            if (!is_file($absoluta)) {
                continue;   // ya no estaba; no es un fallo
            }
            if (@unlink($absoluta)) {
                $borrados++;
            } else {
                $fallidos[] = (string) $ruta;
            }
        }

        return ['borrada' => true, 'archivos' => $borrados, 'no_borrados' => $fallidos];
    }

    /**
     * Ruta en disco de un adjunto, o null si se sale de public/uploads.
     *
     * Lo que hay guardado no es homogéneo —unas filas traen la ruta web
     * ("/uploads/admissions/x.pdf") y otras sólo el nombre—, así que se
     * normaliza y se comprueba que el resultado sigue dentro de uploads
     * antes de borrar nada.
     */
    private function rutaAbsoluta(string $guardada): ?string
    {
        $uploads = realpath((string) \Core\Config::get('paths.uploads'));
        if ($uploads === false) {
            return null;
        }

        $relativa = ltrim(str_replace('\\', '/', $guardada), '/');
        $relativa = preg_replace('#^(public/)?uploads/#', '', $relativa);
        if ($relativa === null || $relativa === '') {
            return null;
        }

        // Se rechaza cualquier ".." y cualquier ruta absoluta ANTES de tocar
        // el disco. Comprobarlo después con realpath no basta: cuando el
        // archivo no existe, realpath devuelve false y lo que se comparaba
        // era la ruta sin resolver, que todavía lleva los ".." dentro.
        foreach (explode('/', $relativa) as $trozo) {
            if ($trozo === '..') {
                return null;
            }
        }
        if (preg_match('#^[a-zA-Z]:#', $relativa)) {
            return null;
        }

        $candidata = $uploads . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativa);
        $real = realpath($candidata) ?: $candidata;

        return str_starts_with($real, $uploads . DIRECTORY_SEPARATOR) ? $real : null;
    }

    public function getAllWithPagination($page = 1, $perPage = 20, $filters = [])
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(given_name LIKE ? OR surname LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        $sql = "SELECT * FROM {$this->table} 
                {$where}
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    public function getCount($filters = [])
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(given_name LIKE ? OR surname LIKE ? OR email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} {$where}";
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] ?? 0;
    }

    /**
     * Actualiza los datos del solicitante y la fecha de la solicitud.
     *
     * No toca el estado ni las notas: eso va por updateStatus, que es lo que
     * usa la pastilla del listado. Sólo se escriben las columnas recibidas,
     * para que añadir un campo al formulario no obligue a tocar esto.
     *
     * Devuelve si la consulta salió adelante. Ojo: Database::update() no
     * devuelve un bool sino el PDOStatement (o false si falló), aunque la
     * documentación del proyecto diga otra cosa; por eso se normaliza aquí
     * en vez de declarar `: bool` y provocar un TypeError con el UPDATE ya
     * ejecutado.
     *
     * @param array<string,mixed> $data
     */
    public function updateDetails(int $id, array $data): bool
    {
        $permitidas = [
            'given_name', 'surname', 'passport_id', 'nationality',
            'date_of_birth', 'country_of_birth', 'education_level',
            'email', 'phone', 'address', 'capacity', 'created_at',
        ];

        $campos = array_intersect_key($data, array_flip($permitidas));
        if (!$campos) {
            return false;
        }

        $campos['updated_at'] = date('Y-m-d H:i:s');

        return (bool) $this->db->update($this->table, $campos, 'id = :id', ['id' => $id]);
    }

    public function updateStatus($id, $status, $notes = null)
    {
        return $this->db->update(
            $this->table,
            [
                'status' => $status,
                'notes' => $notes,
                'updated_at' => date('Y-m-d H:i:s')
            ],
            'id = :id',
            ['id' => $id]
        );
    }

    public function getStatusCounts()
    {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count
                FROM {$this->table}
                GROUP BY status";
        
        $results = $this->db->fetchAll($sql);
        $counts = [
            'pending' => 0,
            'reviewed' => 0,
            'approved' => 0,
            'rejected' => 0
        ];

        foreach ($results as $row) {
            $counts[$row['status']] = $row['count'];
        }

        return $counts;
    }
}
