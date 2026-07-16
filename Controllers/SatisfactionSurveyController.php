<?php

namespace Controllers;

use Core\Database;
use Core\View;
use Core\Security;
use Core\Email;

class SatisfactionSurveyController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function showForm()
    {
        // Fetch active courses (products)
        $productModel = new \Plugins\Ecommerce\Models\Product();
        $courses = $productModel->getActive();

        $view = new View();
        $view->render('public/views/satisfaction-survey', [
            'title' => 'Formulario de Satisfacción',
            'courses' => $courses,
            'csrf_token' => Security::generateCsrfToken()
        ], 'public/views/layout');
    }

    public function submit()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validate CSRF token
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido. Por favor, intente nuevamente.';
            header('Location: /formulario-de-satisfaccion');
            exit;
        }

        // Validate and sanitize input
        $errors = $this->validateSubmission($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /formulario-de-satisfaccion');
            exit;
        }

        // Prepare data
        $data = [
            'first_name' => Security::sanitizeString($_POST['first_name']),
            'last_name' => Security::sanitizeString($_POST['last_name']),
            'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
            'course_name' => Security::sanitizeString($_POST['course_name']),
            'staff_attention_rating' => $_POST['staff_attention_rating'],
            'staff_attention_comments' => Security::sanitizeString($_POST['staff_attention_comments'] ?? ''),
            'training_quality_rating' => $_POST['training_quality_rating'],
            'training_quality_comments' => Security::sanitizeString($_POST['training_quality_comments'] ?? ''),
            'instructor_performance_rating' => $_POST['instructor_performance_rating'],
            'instructor_performance_comments' => Security::sanitizeString($_POST['instructor_performance_comments'] ?? ''),
            'infrastructure_rating' => $_POST['infrastructure_rating'],
            'infrastructure_comments' => Security::sanitizeString($_POST['infrastructure_comments'] ?? ''),
            'survey_date' => $_POST['survey_date']
        ];

        // Insert into database
        try {
            $sql = "INSERT INTO satisfaction_surveys (
                first_name, last_name, email, course_name,
                staff_attention_rating, staff_attention_comments,
                training_quality_rating, training_quality_comments,
                instructor_performance_rating, instructor_performance_comments,
                infrastructure_rating, infrastructure_comments,
                survey_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $this->db->query($sql, [
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['course_name'],
                $data['staff_attention_rating'],
                $data['staff_attention_comments'],
                $data['training_quality_rating'],
                $data['training_quality_comments'],
                $data['instructor_performance_rating'],
                $data['instructor_performance_comments'],
                $data['infrastructure_rating'],
                $data['infrastructure_comments'],
                $data['survey_date']
            ]);

            try {
                Email::sendSurveyNotification($data);
                Email::sendSurveyConfirmation($data);
            } catch (\Exception $e) {
                error_log("Error sending email notification: " . $e->getMessage());
            }

            $_SESSION['success'] = '¡Gracias por tu feedback! Tu encuesta ha sido enviada exitosamente.';
            header('Location: /formulario-de-satisfaccion');
            exit;

        } catch (\Exception $e) {
            error_log("Error saving satisfaction survey: " . $e->getMessage());
            $_SESSION['error'] = 'Hubo un error al enviar tu encuesta. Por favor, intenta nuevamente.';
            header('Location: /formulario-de-satisfaccion');
            exit;
        }
    }

    private function validateSubmission($data)
    {
        $errors = [];

        // Required fields
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'El nombre es requerido';
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Los apellidos son requeridos';
        }

        if (empty($data['email'])) {
            $errors['email'] = 'El email es requerido';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'El email no es válido';
        }

        if (empty($data['course_name'])) {
            $errors['course_name'] = 'Debe seleccionar un curso';
        }

        if (empty($data['staff_attention_rating'])) {
            $errors['staff_attention_rating'] = 'Debe calificar la atención del personal';
        } elseif (!in_array($data['staff_attention_rating'], ['A-Buena', 'B-Regular', 'C-Mala'])) {
            $errors['staff_attention_rating'] = 'Calificación inválida';
        }

        if (empty($data['training_quality_rating'])) {
            $errors['training_quality_rating'] = 'Debe calificar la calidad del entrenamiento';
        } elseif (!in_array($data['training_quality_rating'], ['A-Buena', 'B-Regular', 'C-Mala'])) {
            $errors['training_quality_rating'] = 'Calificación inválida';
        }

        if (empty($data['instructor_performance_rating'])) {
            $errors['instructor_performance_rating'] = 'Debe calificar el desempeño del instructor';
        } elseif (!in_array($data['instructor_performance_rating'], ['A-Buena', 'B-Regular', 'C-Mala'])) {
            $errors['instructor_performance_rating'] = 'Calificación inválida';
        }

        if (empty($data['infrastructure_rating'])) {
            $errors['infrastructure_rating'] = 'Debe calificar las condiciones de infraestructura';
        } elseif (!in_array($data['infrastructure_rating'], ['A-Buena', 'B-Regular', 'C-Mala'])) {
            $errors['infrastructure_rating'] = 'Calificación inválida';
        }

        if (empty($data['survey_date'])) {
            $errors['survey_date'] = 'La fecha es requerida';
        } elseif (!strtotime($data['survey_date'])) {
            $errors['survey_date'] = 'Fecha inválida';
        }

        return $errors;
    }
}
