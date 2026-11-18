<?php

namespace Plugins\Elearning\Models;

use Core\Model;

/**
 * Chat interno alumno ↔ instructor/administración.
 *
 * Todas las reglas de quién puede ver o escribir están aquí y no en el portal:
 * el portal es un cliente más del API y cualquiera podría llamarlo directamente.
 *
 *  - El alumno abre conversaciones (sobre un curso en el que está inscrito, o
 *    una consulta general) y solo ve las suyas.
 *  - Administrador y director académico ven y responden todas.
 *  - El instructor ve y responde las de los cursos que imparte.
 *  - Nadie más participa. El personal no abre conversaciones: responde.
 */
class Chat extends Model
{
    protected $table = 'chat_conversations';

    public const MAX_LONGITUD   = 2000;   // caracteres por mensaje
    public const MAX_POR_MINUTO = 20;     // mensajes por usuario y minuto
    public const MAX_ASUNTO     = 150;

    private const ROLES_TODO = ['admin', 'academic_director'];

    /** Rol del usuario, o null si no existe. */
    public function rolDe(int $userId): ?string
    {
        $u = $this->db->fetchOne('SELECT role FROM users WHERE id = ? LIMIT 1', [$userId]);
        return $u ? (string) $u['role'] : null;
    }

    public function esPersonal(?string $rol): bool
    {
        return in_array($rol, [...self::ROLES_TODO, 'teacher'], true);
    }

    /**
     * Condición SQL (con alias "c" para la conversación) que limita lo que
     * ve cada usuario, y sus parámetros. null = no puede ver ninguna.
     *
     * @return array{0:string,1:array}|null
     */
    private function visibilidad(int $userId, ?string $rol): ?array
    {
        if ($rol === 'student') {
            return ['c.student_id = ?', [$userId]];
        }
        if (in_array($rol, self::ROLES_TODO, true)) {
            return ['1 = 1', []];
        }
        if ($rol === 'teacher') {
            return ['c.course_id IN (SELECT id FROM lms_courses WHERE teacher_id = ?)', [$userId]];
        }
        return null;
    }

    /**
     * Conversaciones del usuario, las más recientes primero, con el último
     * mensaje y cuántos tiene sin leer.
     */
    public function conversaciones(int $userId, int $limite = 100): array
    {
        $rol = $this->rolDe($userId);
        $vis = $this->visibilidad($userId, $rol);
        if (!$vis) {
            return [];
        }
        $esAlumno = $rol === 'student';
        $leido = $esAlumno ? 'c.student_read_until' : 'c.staff_read_until';
        // Sin leer: mensajes de la otra parte posteriores a lo ya leído
        $otra  = $esAlumno ? 'm.sender_id <> c.student_id' : 'm.sender_id = c.student_id';

        $sql = "SELECT c.id, c.subject, c.course_id, c.student_id, c.created_at, c.last_message_at,
                       s.name AS student_name, k.title AS course_title,
                       (SELECT m2.body FROM chat_messages m2 WHERE m2.conversation_id = c.id ORDER BY m2.id DESC LIMIT 1) AS ultimo,
                       (SELECT COUNT(*) FROM chat_messages m
                         WHERE m.conversation_id = c.id AND m.id > {$leido} AND {$otra}) AS sin_leer
                FROM chat_conversations c
                JOIN users s ON s.id = c.student_id
                LEFT JOIN lms_courses k ON k.id = c.course_id
                WHERE {$vis[0]}
                ORDER BY COALESCE(c.last_message_at, c.created_at) DESC, c.id DESC
                LIMIT " . max(1, min(500, $limite));

        return $this->db->fetchAll($sql, $vis[1]);
    }

    /** Una conversación, solo si el usuario puede verla. */
    public function conversacion(int $userId, int $id): ?array
    {
        $rol = $this->rolDe($userId);
        $vis = $this->visibilidad($userId, $rol);
        if (!$vis) {
            return null;
        }
        $fila = $this->db->fetchOne(
            "SELECT c.*, s.name AS student_name, k.title AS course_title
               FROM chat_conversations c
               JOIN users s ON s.id = c.student_id
               LEFT JOIN lms_courses k ON k.id = c.course_id
              WHERE c.id = ? AND ({$vis[0]})",
            array_merge([$id], $vis[1])
        );
        if (!$fila) {
            return null;
        }
        $fila['mi_rol'] = $rol;
        return $fila;
    }

    /**
     * Abre una conversación con su primer mensaje.
     *
     * @return array{id:int}|array{error:string}
     */
    public function abrir(int $studentId, ?int $courseId, string $asunto, string $cuerpo): array
    {
        if ($this->rolDe($studentId) !== 'student') {
            return ['error' => 'Solo el alumnado puede abrir una conversación.'];
        }

        $asunto = trim(preg_replace('/\s+/', ' ', strip_tags($asunto)));
        if ($asunto === '') {
            return ['error' => 'Escribe un asunto.'];
        }
        $asunto = mb_substr($asunto, 0, self::MAX_ASUNTO);

        if ($courseId) {
            $inscrito = $this->db->fetchOne(
                'SELECT 1 AS ok FROM lms_enrollments WHERE student_id = ? AND course_id = ? LIMIT 1',
                [$studentId, $courseId]
            );
            if (!$inscrito) {
                return ['error' => 'Solo puedes escribir sobre cursos en los que estás inscrito.'];
            }
        }

        $err = $this->validarCuerpo($cuerpo) ?? $this->limitarRitmo($studentId);
        if ($err) {
            return ['error' => $err];
        }

        $id = $this->db->insert('chat_conversations', [
            'student_id' => $studentId,
            'course_id'  => $courseId ?: null,
            'subject'    => $asunto,
        ]);
        if (!$id) {
            return ['error' => 'No se pudo crear la conversación.'];
        }
        $this->insertarMensaje((int) $id, $studentId, $cuerpo);

        return ['id' => (int) $id];
    }

    /**
     * Añade un mensaje a una conversación que el usuario pueda ver.
     *
     * @return array{id:int}|array{error:string,codigo:int}
     */
    public function responder(int $userId, int $conversacionId, string $cuerpo): array
    {
        if (!$this->conversacion($userId, $conversacionId)) {
            // Mismo mensaje exista o no: no se revela qué conversaciones hay.
            return ['error' => 'Conversación no encontrada.', 'codigo' => 404];
        }
        $err = $this->validarCuerpo($cuerpo) ?? $this->limitarRitmo($userId);
        if ($err) {
            return ['error' => $err, 'codigo' => 422];
        }
        return ['id' => $this->insertarMensaje($conversacionId, $userId, $cuerpo)];
    }

    /**
     * A quién avisar de un mensaje nuevo: el alumno de la conversación, el instructor del
     * curso y la administración (admin y dirección académica), sin el remitente.
     *
     * @return array<int,array{id:int,name:string,email:string,role:string}>
     */
    public function destinatarios(int $conversacionId, int $remitenteId): array
    {
        $filas = $this->db->fetchAll(
            "SELECT DISTINCT u.id, u.name, u.email, u.role
               FROM users u
              WHERE u.id <> ?
                AND (
                      u.id = (SELECT student_id FROM chat_conversations WHERE id = ?)
                   OR u.id = (SELECT c.teacher_id FROM chat_conversations cc
                                JOIN lms_courses c ON c.id = cc.course_id WHERE cc.id = ?)
                   OR u.role IN ('admin', 'academic_director')
                )",
            [$remitenteId, $conversacionId, $conversacionId]
        );
        return array_map(static fn($f) => [
            'id' => (int) $f['id'], 'name' => (string) $f['name'],
            'email' => (string) $f['email'], 'role' => (string) $f['role'],
        ], $filas);
    }

    /**
     * Mensajes de una conversación, de más antiguo a más reciente.
     * Con $despuesDe solo los posteriores a ese id (sondeo). Marca como leído
     * lo que el usuario acaba de recibir.
     *
     * @return array<int,array>|null null si no puede verla.
     */
    public function mensajes(int $userId, int $conversacionId, int $despuesDe = 0, int $limite = 200): ?array
    {
        $conv = $this->conversacion($userId, $conversacionId);
        if (!$conv) {
            return null;
        }

        $filas = $this->db->fetchAll(
            "SELECT m.id, m.sender_id, m.body, m.created_at, u.name AS sender_name,
                    (m.sender_id = c.student_id) AS de_alumno
               FROM chat_messages m
               JOIN chat_conversations c ON c.id = m.conversation_id
               JOIN users u ON u.id = m.sender_id
              WHERE m.conversation_id = ? AND m.id > ?
              ORDER BY m.id ASC
              LIMIT " . max(1, min(500, $limite)),
            [$conversacionId, max(0, $despuesDe)]
        );

        if ($filas) {
            $ultimo = (int) end($filas)['id'];
            $col = $conv['mi_rol'] === 'student' ? 'student_read_until' : 'staff_read_until';
            // GREATEST: nunca retrocede lo ya leído (sondeos fuera de orden)
            $this->db->query(
                "UPDATE chat_conversations SET {$col} = GREATEST({$col}, ?) WHERE id = ?",
                [$ultimo, $conversacionId]
            );
        }

        return array_map(static function ($f) use ($userId) {
            return [
                'id'          => (int) $f['id'],
                'sender_id'   => (int) $f['sender_id'],
                'sender_name' => (string) $f['sender_name'],
                'body'        => (string) $f['body'],
                'created_at'  => (string) $f['created_at'],
                'mio'         => (int) $f['sender_id'] === $userId,
                'de_alumno'   => (bool) $f['de_alumno'],
            ];
        }, $filas);
    }

    /** Total de mensajes sin leer, para el contador del menú. */
    public function sinLeer(int $userId): int
    {
        $n = 0;
        foreach ($this->conversaciones($userId, 500) as $c) {
            $n += (int) $c['sin_leer'];
        }
        return $n;
    }

    // ─── Internos ────────────────────────────────────────────────────

    private function validarCuerpo(string $cuerpo): ?string
    {
        $cuerpo = trim($cuerpo);
        if ($cuerpo === '') {
            return 'El mensaje está vacío.';
        }
        if (mb_strlen($cuerpo) > self::MAX_LONGITUD) {
            return 'El mensaje es demasiado largo (máximo ' . self::MAX_LONGITUD . ' caracteres).';
        }
        return null;
    }

    private function limitarRitmo(int $userId): ?string
    {
        $r = $this->db->fetchOne(
            'SELECT COUNT(*) AS n FROM chat_messages WHERE sender_id = ? AND created_at > (NOW() - INTERVAL 1 MINUTE)',
            [$userId]
        );
        return (int) ($r['n'] ?? 0) >= self::MAX_POR_MINUTO
            ? 'Estás enviando mensajes muy rápido. Espera un momento.'
            : null;
    }

    private function insertarMensaje(int $conversacionId, int $userId, string $cuerpo): int
    {
        $id = (int) $this->db->insert('chat_messages', [
            'conversation_id' => $conversacionId,
            'sender_id'       => $userId,
            'body'            => trim($cuerpo),
        ]);
        if ($id) {
            $this->db->query('UPDATE chat_conversations SET last_message_at = NOW() WHERE id = ?', [$conversacionId]);
        }
        return $id;
    }
}
