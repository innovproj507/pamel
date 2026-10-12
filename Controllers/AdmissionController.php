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

    /** Nombre legible de cada documento, para los mensajes de error. */
    private const FILE_LABELS = [
        'cedula'            => 'Cédula / Pasaporte',
        'certificado_salud' => 'Certificado de salud',
    ];

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
            $required = ['full_name', 'id_passport', 'dob', 'nationality', 'email', 'address', 'phone', 'course'];

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
                        'course'      => 'Curso',
                    ];
                    throw new \Exception("El campo \"{$labels[$field]}\" es requerido.");
                }
            }

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
                'country_of_birth'=> '',
                'email'           => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'address'         => $sanitize($_POST['address']),
                'phone'           => $sanitize($_POST['phone']),
                'course'          => $sanitize($_POST['course']),
                'course_mode'     => in_array($_POST['course_mode'] ?? 'new', ['new', 'refresher'], true)
                                     ? $_POST['course_mode'] : 'new',
                'consent_accepted'=> isset($_POST['consent']) ? 1 : 0,
                'capacity'        => '',
            ];

            // Handle file uploads
            $uploadDir         = \Core\Config::get('paths.root') . '/public/uploads/admissions/';
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            $maxFileSize       = self::maxUploadBytes();
            $maxLabel          = self::formatSize($maxFileSize);

            $filesToUpload = [
                'id_file'                  => 'cedula',
                'health_certificate_file'  => 'certificado_salud',
            ];

            foreach ($filesToUpload as $dbField => $inputName) {
                $label = self::FILE_LABELS[$inputName] ?? $inputName;
                $error = $_FILES[$inputName]['error'] ?? UPLOAD_ERR_NO_FILE;

                if ($error === UPLOAD_ERR_OK) {
                    $tmpPath   = $_FILES[$inputName]['tmp_name'];
                    $fileName  = $_FILES[$inputName]['name'];
                    $fileSize  = (int) $_FILES[$inputName]['size'];
                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (!\in_array($extension, $allowedExtensions)) {
                        throw new \Exception("«{$label}»: formato no permitido. Use JPG, PNG o PDF.");
                    }
                    if ($fileSize > $maxFileSize) {
                        throw new \Exception(sprintf(
                            '«%s»: el archivo pesa %s y el máximo permitido es %s. '
                            . 'Comprime el PDF o reduce la resolución del escaneo e inténtalo de nuevo.',
                            $label,
                            self::formatSize($fileSize),
                            $maxLabel
                        ));
                    }

                    $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
                    $mime = mime_content_type($tmpPath);
                    if (!\in_array($mime, $allowedMimes, true)) {
                        throw new \Exception("«{$label}»: tipo de archivo no permitido.");
                    }

                    $newName  = uniqid($inputName . '_', true) . '.' . $extension;
                    $destPath = $uploadDir . $newName;

                    if (move_uploaded_file($tmpPath, $destPath)) {
                        $data[$dbField] = '/public/uploads/admissions/' . $newName;
                    } else {
                        throw new \Exception("«{$label}»: no se pudo guardar el archivo en el servidor.");
                    }
                } elseif ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                    // PHP lo cortó antes de llegar aquí: el límite del servidor manda.
                    throw new \Exception(
                        "«{$label}»: el archivo supera el máximo permitido por el servidor ({$maxLabel})."
                    );
                } elseif ($error === UPLOAD_ERR_PARTIAL) {
                    throw new \Exception("«{$label}»: la carga se interrumpió. Vuelve a intentarlo.");
                } elseif ($error !== UPLOAD_ERR_NO_FILE) {
                    error_log("Admisión: error de carga {$error} en {$inputName}");
                    throw new \Exception("«{$label}»: no se pudo procesar el archivo. Inténtalo de nuevo.");
                } else {
                    $data[$dbField] = null;
                }
            }

            // ── Adjuntos exigidos por los requisitos del curso ──────────
            // Se validan ANTES de crear la solicitud: si algo falla, no se
            // guarda una solicitud a medias.
            $adjuntos = $this->validarAdjuntosDeRequisitos(
                $data['course'],
                $data['course_mode'],
                $uploadDir,
                $allowedExtensions,
                $maxFileSize,
                $maxLabel
            );

            // Save to database
            $requestId = $this->admissionRequest->create($data);

            if ($requestId && $adjuntos) {
                $db = \Core\Database::getInstance();
                foreach ($adjuntos as $a) {
                    $db->insert('admission_request_files', [
                        'admission_request_id' => (int) $requestId,
                        'requirement_id'       => $a['requirement_id'],
                        'label'                => $a['label'],
                        'file_path'            => $a['file_path'],
                        'original_name'        => $a['original_name'],
                        'size_bytes'           => $a['size_bytes'],
                    ]);
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
     * Valida y mueve los documentos que piden los requisitos del curso.
     *
     * Devuelve los datos listos para insertar en admission_request_files.
     * Lanza excepción ante cualquier problema, de modo que la solicitud no
     * llegue a crearse si los adjuntos no están completos.
     *
     * @return array<array{requirement_id:int,label:string,file_path:string,original_name:string,size_bytes:int}>
     */
    private function validarAdjuntosDeRequisitos(
        string $courseName,
        string $mode,
        string $uploadDir,
        array $allowedExtensions,
        int $maxFileSize,
        string $maxLabel
    ): array {
        $db = \Core\Database::getInstance();

        $producto = $db->fetchOne(
            "SELECT id FROM products WHERE name = ? AND status = 'active' LIMIT 1",
            [$courseName]
        );
        if (!$producto) {
            return [];   // curso sin ficha de producto: no hay requisitos que exigir
        }

        $modelo    = new \Core\Models\CourseRequirement();
        $exigidos  = $modelo->adjuntosRequeridos((int) $producto['id'], $mode);
        if (!$exigidos) {
            return [];
        }

        $entrantes = $_FILES['req_file'] ?? null;
        $resultado = [];

        foreach ($exigidos as $reqId => $label) {
            $error = $entrantes['error'][$reqId] ?? UPLOAD_ERR_NO_FILE;

            if ($error === UPLOAD_ERR_NO_FILE) {
                throw new \Exception("Falta adjuntar el documento: «{$label}».");
            }
            if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
                throw new \Exception("«{$label}»: el archivo supera el máximo permitido ({$maxLabel}).");
            }
            if ($error !== UPLOAD_ERR_OK) {
                error_log("Admisión: error de carga {$error} en el requisito {$reqId}");
                throw new \Exception("«{$label}»: no se pudo procesar el archivo. Inténtalo de nuevo.");
            }

            $tmp       = $entrantes['tmp_name'][$reqId];
            $nombre    = (string) $entrantes['name'][$reqId];
            $tamano    = (int) $entrantes['size'][$reqId];
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

            $destino = uniqid('req_' . $reqId . '_', true) . '.' . $extension;
            if (!move_uploaded_file($tmp, $uploadDir . $destino)) {
                throw new \Exception("«{$label}»: no se pudo guardar el archivo en el servidor.");
            }

            $resultado[] = [
                'requirement_id' => (int) $reqId,
                'label'          => $label,
                'file_path'      => '/public/uploads/admissions/' . $destino,
                'original_name'  => mb_substr($nombre, 0, 255),
                'size_bytes'     => $tamano,
            ];
        }

        return $resultado;
    }

}
