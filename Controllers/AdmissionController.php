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

            // Save to database
            $this->admissionRequest->create($data);

            $emailData = $data;
            $emailData['full_name'] = $fullName;
            Email::sendAdmissionNotification($emailData);
            Email::sendAdmissionConfirmation($emailData);

            echo json_encode([
                'success' => true,
                'message' => '¡Solicitud enviada exitosamente! Recibirá un correo con los próximos pasos.',
            ]);

        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
