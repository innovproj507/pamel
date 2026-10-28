<?php

namespace Controllers;

use Core\Email;
use Core\Models\AdmissionRequest;

class AdmissionController
{
    private $admissionRequest;

    public function __construct()
    {
        $this->admissionRequest = new AdmissionRequest();
    }

    /** Límite propio para los documentos adjuntos. */
    private const MAX_UPLOAD_BYTES = 10 * 1024 * 1024; // 10 MB

    /**
     * Tope de cursos por solicitud. No es una regla de negocio, es un freno:
     * cada curso añade requisitos y adjuntos al mismo envío, y sin límite un
     * robot podría mandar los 66 cursos de golpe.
     */
    private const MAX_CURSOS = 10;

    /**
     * Niveles educativos admitidos. Se guarda la etiqueta en español porque
     * es la que se imprime en el formulario oficial F-41, con independencia
     * del idioma en que el solicitante rellene la página.
     *
     * Pública porque el panel edita el mismo campo y tiene que ofrecer y
     * aceptar exactamente esta lista.
     */
    public const NIVELES_EDUCATIVOS = [
        'Primaria',
        'Secundaria',
        'Técnico / Vocacional',
        'Universitario',
        'Postgrado',
        'Otro',
    ];

    /** Sólo admite un nivel de la lista; cualquier otra cosa queda vacía. */
    private static function nivelEducativoValido($valor): string
    {
        $valor = trim((string) $valor);

        return in_array($valor, self::NIVELES_EDUCATIVOS, true) ? $valor : '';
    }

    /**
     * Tamaño máximo real por archivo: el menor entre nuestro límite y lo que
     * permita el servidor (upload_max_filesize / post_max_size). Sin esto,
     * anunciaríamos un máximo que PHP rechazaría antes de llegar aquí.
     */
    public static function maxUploadBytes(): int
    {
        $limits = array_filter([
            self::iniBytes((string) ini_get('upload_max_filesize')),
            self::iniBytes((string) ini_get('post_max_size')),
        ]);

        return $limits ? (int) min(self::MAX_UPLOAD_BYTES, min($limits)) : self::MAX_UPLOAD_BYTES;
    }

    /** Convierte los valores tipo "8M" o "2G" de php.ini a bytes. */
    private static function iniBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 0;
        }

        $unit   = strtolower($value[strlen($value) - 1]);
        $number = (int) $value;

        switch ($unit) {
            case 'g': return $number * 1024 * 1024 * 1024;
            case 'm': return $number * 1024 * 1024;
            case 'k': return $number * 1024;
            default:  return $number;
        }
    }

    /** Formatea bytes para mostrarlos al usuario. */
    private static function formatSize(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 1, ',', '.') . ' MB';
        }
        return number_format(max(0, $bytes) / 1024, 0, ',', '.') . ' KB';
    }
    public function submit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
            // Si el envío supera post_max_size, PHP descarta $_POST y $_FILES por
            // completo; sin este aviso el usuario vería "el campo X es requerido".
            if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
                throw new \Exception(sprintf(
                    'El envío es demasiado grande (%s). Cada documento adjunto puede pesar como máximo %s.',
                    self::formatSize((int) $_SERVER['CONTENT_LENGTH']),
                    self::formatSize(self::maxUploadBytes())
                ));
            }

            // Validate required fields using the actual HTML form field names
            $required = [
                'full_name', 'id_passport', 'dob', 'nationality', 'country_of_birth',
                'education_level', 'email', 'address', 'phone',
            ];

            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    $labels = [
                        'full_name'   => 'Nombre completo',
                        'id_passport' => 'Cédula / Pasaporte',
                        'dob'         => 'Fecha de nacimiento',
                        'nationality' => 'Nacionalidad',
                        'email'       => 'Email',
                        'address'     => 'Dirección',
                        'phone'       => 'Teléfono',
                    ];
                    throw new \Exception("El campo \"{$labels[$field]}\" es requerido.");
                }
            }

            // Los cursos llegan como lista de ids, cada uno con su modalidad.
            $cursos = $this->cursosElegidos();

            // Validate consent
            if (empty($_POST['consent'])) {
                throw new \Exception("Debe aceptar los términos y condiciones para continuar.");
            }

            // Validate email
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email inválido.');
            }

            // Split full_name into given_name + surname for DB storage
            $fullName  = trim($_POST['full_name']);
            $nameParts = explode(' ', $fullName, 2);
            $givenName = $nameParts[0];
            $surname   = $nameParts[1] ?? '';

            // Sanitize
            $sanitize = fn($v) => htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');

            $data = [
                'given_name'      => $sanitize($givenName),
                'surname'         => $sanitize($surname),
                'passport_id'     => $sanitize($_POST['id_passport']),
                'date_of_birth'   => $sanitize($_POST['dob']),
                'nationality'     => $sanitize($_POST['nationality']),
                'country_of_birth'=> $sanitize($_POST['country_of_birth'] ?? ''),
                'education_level' => self::nivelEducativoValido($_POST['education_level'] ?? ''),
                'email'           => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'address'         => $sanitize($_POST['address']),
                'phone'           => $sanitize($_POST['phone']),
                // Resumen de los cursos, para el listado del panel y para las
                // 101 solicitudes antiguas que sólo tienen esta columna. El
                // detalle de cada curso va en admission_request_courses.
                'course'          => self::resumenCursos($cursos),
                'course_mode'     => $cursos[0]['mode'],
                'consent_accepted'=> isset($_POST['consent']) ? 1 : 0,
                'capacity'        => '',
            ];

            // Handle file uploads
            $uploadDir         = \Core\Config::get('paths.root') . '/public/uploads/admissions/';
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            $maxFileSize       = self::maxUploadBytes();
            $maxLabel          = self::formatSize($maxFileSize);

            // El formulario ya no trae campos fijos de identificación ni de
            // certificado médico: ahora llegan como requisitos del curso,
            // igual que los demás. Las columnas id_file y
            // health_certificate_file se conservan porque las solicitudes
            // anteriores las usan, pero ya no se rellenan.
            $data['id_file']                 = null;
            $data['health_certificate_file'] = null;

            // ── Adjuntos exigidos por los requisitos de los cursos ──────
            // Se validan ANTES de crear la solicitud: si algo falla, no se
            // guarda una solicitud a medias.
            $adjuntos = $this->validarAdjuntosDeRequisitos(
                $cursos,
                $uploadDir,
                $allowedExtensions,
                $maxFileSize,
                $maxLabel
            );

            // Save to database
            $requestId = $this->admissionRequest->create($data);

            if ($requestId) {
                $this->admissionRequest->saveCourses((int) $requestId, $cursos);
            }

            if ($requestId && $adjuntos) {
                $db = \Core\Database::getInstance();
                foreach ($adjuntos as $a) {
                    $fileId = $db->insert('admission_request_files', [
                        'admission_request_id' => (int) $requestId,
                        // Se guarda el primero para no romper lo que ya lee
                        // esta columna; la lista completa va en la tabla de
                        // enlace, porque un mismo documento puede cubrir el
                        // requisito de varios cursos a la vez.
                        'requirement_id'       => $a['requirement_ids'][0],
                        'label'                => $a['label'],
                        'file_path'            => $a['file_path'],
                        'original_name'        => $a['original_name'],
                        'size_bytes'           => $a['size_bytes'],
                    ]);

                    if ($fileId) {
                        foreach ($a['requirement_ids'] as $reqId) {
                            $db->insert('admission_request_file_requirements', [
                                'file_id'        => (int) $fileId,
                                'requirement_id' => (int) $reqId,
                            ]);
                        }
                    }
                }
            }

            // Se responde ANTES de los correos: la solicitud ya está guardada
            // y un SMTP lento o caído no debe dejar al solicitante sin
            // confirmación (acabaría reenviando y duplicando la solicitud).
            echo json_encode([
                'success' => true,
                'message' => '¡Solicitud enviada exitosamente! Recibirá un correo con los próximos pasos.',
            ]);

            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            } else {
                while (ob_get_level() > 0) {
                    @ob_end_flush();
                }
                @flush();
            }

            $emailData = $data;
            $emailData['full_name'] = $fullName;
            // La lista completa, para el correo y para marcar las casillas
            // del Word: en `course` sólo va el resumen.
            $emailData['courses'] = $cursos;
            $emailData['id']      = $requestId;

            try {
                Email::sendAdmissionNotification($emailData);
                Email::sendAdmissionConfirmation($emailData);
            } catch (\Throwable $e) {
                error_log('Admisión: fallo al enviar los correos de la solicitud '
                    . $requestId . ': ' . $e->getMessage());
            }

            return;

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Cursos elegidos en el formulario, cada uno con su modalidad.
     *
     * Llegan como courses[] (ids de producto) y course_mode[<id>]. Se
     * resuelven contra la base: así el nombre guardado es el oficial y no
     * el que viniera en el POST, y de paso se descarta cualquier id que no
     * corresponda a un curso activo.
     *
     * @return array<array{product_id:int,course_name:string,mode:string}>
     */
    private function cursosElegidos(): array
    {
        $ids = $_POST['courses'] ?? [];
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        // Enteros, sin repetidos y sin ceros, conservando el orden de elección.
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if (!$ids) {
            throw new \Exception('Debe seleccionar al menos un curso.');
        }
        if (count($ids) > self::MAX_CURSOS) {
            throw new \Exception(sprintf(
                'Puede solicitar como máximo %d cursos en una misma solicitud.',
                self::MAX_CURSOS
            ));
        }

        $db     = \Core\Database::getInstance();
        $modos  = is_array($_POST['course_mode'] ?? null) ? $_POST['course_mode'] : [];
        $cursos = [];

        foreach ($ids as $id) {
            $producto = $db->fetchOne(
                "SELECT id, name FROM products WHERE id = ? AND status = 'active' LIMIT 1",
                [$id]
            );
            if (!$producto) {
                throw new \Exception('Uno de los cursos seleccionados ya no está disponible. Vuelva a elegirlo.');
            }

            $modo = $modos[$id] ?? 'new';

            $cursos[] = [
                'product_id'  => (int) $producto['id'],
                'course_name' => $producto['name'],
                'mode'        => in_array($modo, ['new', 'refresher'], true) ? $modo : 'new',
            ];
        }

        return $cursos;
    }

    /**
     * Texto corto de los cursos para la columna `course`, que es VARCHAR(200).
     *
     * Con varios cursos no caben todos los nombres, así que se guarda el
     * primero y cuántos más hay. El detalle completo está en
     * admission_request_courses.
     */
    private static function resumenCursos(array $cursos): string
    {
        $primero = $cursos[0]['course_name'];
        $resto   = count($cursos) - 1;

        if ($resto < 1) {
            return mb_substr($primero, 0, 200);
        }

        $sufijo = " (+{$resto})";

        return mb_substr($primero, 0, 200 - mb_strlen($sufijo)) . $sufijo;
    }

    /**
     * Valida y mueve los documentos que piden los requisitos de los cursos.
     *
     * Los requisitos de todos los cursos elegidos se unifican por texto: si
     * cuatro cursos piden «ID / PASSPORT», se pide un solo archivo y ese
     * archivo queda apuntado contra los cuatro requisitos. Sin esto, el
     * solicitante subiría su pasaporte cuatro veces.
     *
     * Devuelve los datos listos para insertar en admission_request_files.
     * Lanza excepción ante cualquier problema, de modo que la solicitud no
     * llegue a crearse si los adjuntos no están completos.
     *
     * @param array<array{product_id:int,course_name:string,mode:string}> $cursos
     * @return array<array{requirement_ids:int[],label:string,file_path:string,original_name:string,size_bytes:int}>
     */
    private function validarAdjuntosDeRequisitos(
        array $cursos,
        string $uploadDir,
        array $allowedExtensions,
        int $maxFileSize,
        string $maxLabel
    ): array {
        $modelo = new \Core\Models\CourseRequirement();

        // La misma función que alimenta el formulario: si divergieran, aquí
        // se pedirían archivos que la página nunca llegó a mostrar.
        $grupos = array_filter(
            $modelo->unificadosParaCursos($cursos),
            static fn($g) => $g['adjunto']
        );
        if (!$grupos) {
            return [];
        }

        $entrantes = $_FILES['req_file'] ?? null;
        $resultado = [];

        foreach ($grupos as $g) {
            $clave = $g['grupo'];
            $label = $g['label'];
            $error = $entrantes['error'][$clave] ?? UPLOAD_ERR_NO_FILE;

            if ($error === UPLOAD_ERR_NO_FILE) {
                throw new \Exception("Falta adjuntar el documento: «{$label}».");
            }
            if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                throw new \Exception("«{$label}»: el archivo supera el máximo permitido ({$maxLabel}).");
            }
            if ($error !== UPLOAD_ERR_OK) {
                error_log("Admisión: error de carga {$error} en el requisito «{$label}»");
                throw new \Exception("«{$label}»: no se pudo procesar el archivo. Inténtalo de nuevo.");
            }

            $tmp       = $entrantes['tmp_name'][$clave];
            $nombre    = (string) $entrantes['name'][$clave];
            $tamano    = (int) $entrantes['size'][$clave];
            $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

            if (!\in_array($extension, $allowedExtensions, true)) {
                throw new \Exception("«{$label}»: formato no permitido. Use JPG, PNG o PDF.");
            }
            if ($tamano > $maxFileSize) {
                throw new \Exception(sprintf(
                    '«%s»: el archivo pesa %s y el máximo permitido es %s.',
                    $label,
                    self::formatSize($tamano),
                    $maxLabel
                ));
            }

            $mime = mime_content_type($tmp);
            if (!\in_array($mime, ['image/jpeg', 'image/png', 'application/pdf'], true)) {
                throw new \Exception("«{$label}»: tipo de archivo no permitido.");
            }

            $destino = uniqid('req_' . $clave . '_', true) . '.' . $extension;
            if (!move_uploaded_file($tmp, $uploadDir . $destino)) {
                throw new \Exception("«{$label}»: no se pudo guardar el archivo en el servidor.");
            }

            $resultado[] = [
                // Todos los requisitos que este único archivo satisface.
                'requirement_ids' => $g['requirement_ids'],
                'label'          => $label,
                // El servidor sirve public/ como raíz, así que la URL NO
                // lleva ese segmento: con /public delante daba 404 y los
                // adjuntos no se podían abrir desde el panel.
                'file_path'      => '/uploads/admissions/' . $destino,
                'original_name'  => mb_substr($nombre, 0, 255),
                'size_bytes'     => $tamano,
            ];
        }

        return $resultado;
    }

}
