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

    public function submit()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        try {
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
            $uploadDir        = \Core\Config::get('paths.root') . '/public/uploads/admissions/';
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            $maxFileSize      = 5 * 1024 * 1024; // 5 MB

            $filesToUpload = [
                'id_file'                  => 'cedula',
                'health_certificate_file'  => 'certificado_salud',
            ];

            foreach ($filesToUpload as $dbField => $inputName) {
                if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
                    $tmpPath   = $_FILES[$inputName]['tmp_name'];
                    $fileName  = $_FILES[$inputName]['name'];
                    $fileSize  = $_FILES[$inputName]['size'];
                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (!\in_array($extension, $allowedExtensions)) {
                        throw new \Exception("Formato no permitido para {$inputName}. Use JPG, PNG o PDF.");
                    }
                    if ($fileSize > $maxFileSize) {
                        throw new \Exception("El archivo {$inputName} supera el máximo de 5 MB.");
                    }

                    $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
                    $mime = mime_content_type($tmpPath);
                    if (!\in_array($mime, $allowedMimes, true)) {
                        throw new \Exception("Tipo de archivo no permitido para {$inputName}.");
                    }

                    $newName  = uniqid($inputName . '_', true) . '.' . $extension;
                    $destPath = $uploadDir . $newName;

                    if (move_uploaded_file($tmpPath, $destPath)) {
                        $data[$dbField] = '/public/uploads/admissions/' . $newName;
                    } else {
                        throw new \Exception("Error al guardar el archivo {$inputName}.");
                    }
                } elseif (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] !== UPLOAD_ERR_NO_FILE) {
                    throw new \Exception("Error en la carga del archivo {$inputName}: " . $_FILES[$inputName]['error']);
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
