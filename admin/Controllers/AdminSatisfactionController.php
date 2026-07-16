<?php

namespace Admin\Controllers;

use Core\Database;
use Core\View;
use Core\Auth;
use Core\Security;
use Core\WordGenerator;

class AdminSatisfactionController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index()
    {
        // Require admin authentication
        Auth::getInstance()->requireAdmin();

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Filters
        $courseFilter = $_GET['course'] ?? '';
        $ratingFilter = $_GET['rating'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        // Build query
        $where = [];
        $params = [];

        if ($courseFilter) {
            $where[] = "course_name LIKE ?";
            $params[] = "%$courseFilter%";
        }

        if ($ratingFilter) {
            $where[] = "(staff_attention_rating = ? OR training_quality_rating = ? OR instructor_performance_rating = ?)";
            $params[] = $ratingFilter;
            $params[] = $ratingFilter;
            $params[] = $ratingFilter;
        }

        if ($dateFrom) {
            $where[] = "survey_date >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $where[] = "survey_date <= ?";
            $params[] = $dateTo;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM satisfaction_surveys $whereClause";
        $totalResult = $this->db->fetchOne($countSql, $params);
        $total = $totalResult['total'] ?? 0;
        $totalPages = ceil($total / $perPage);

        // Get surveys
        $sql = "SELECT * FROM satisfaction_surveys 
                $whereClause 
                ORDER BY created_at DESC 
                LIMIT $perPage OFFSET $offset";
        
        $surveys = $this->db->fetchAll($sql, $params);

        // Get unique courses for filter
        $courses = $this->db->fetchAll("SELECT DISTINCT course_name FROM satisfaction_surveys ORDER BY course_name");

        $view = new View();
        $view->render('admin/views/satisfaction/index', [
            'surveys' => $surveys,
            'courses' => $courses,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'filters' => [
                'course' => $courseFilter,
                'rating' => $ratingFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        ], 'admin/views/layout');
    }

    public function show($id)
    {
        // Require admin authentication
        Auth::getInstance()->requireAdmin();

        // Validate ID
        if (!Security::validateId($id)) {
            header('Location: /manager/satisfaction-surveys');
            exit;
        }

        $survey = $this->db->fetchOne("SELECT * FROM satisfaction_surveys WHERE id = ?", [$id]);

        if (!$survey) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = 'Encuesta no encontrada';
            header('Location: /manager/satisfaction-surveys');
            exit;
        }

        $view = new View();
        $view->render('admin/views/satisfaction/show', [
            'survey' => $survey
        ], 'admin/views/layout');
    }

    public function delete($id)
    {
        // Require admin authentication
        Auth::getInstance()->requireAdmin();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Validate CSRF
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            header('Location: /manager/satisfaction-surveys');
            exit;
        }

        // Validate ID
        if (!Security::validateId($id)) {
            $_SESSION['error'] = 'ID inválido';
            header('Location: /manager/satisfaction-surveys');
            exit;
        }

        try {
            $this->db->query("DELETE FROM satisfaction_surveys WHERE id = ?", [$id]);
            $_SESSION['success'] = 'Encuesta eliminada exitosamente';
        } catch (\Exception $e) {
            error_log("Error deleting survey: " . $e->getMessage());
            $_SESSION['error'] = 'Error al eliminar la encuesta';
        }

        header('Location: /manager/satisfaction-surveys');
        exit;
    }

    public function downloadWord($id)
    {
        // Require admin authentication
        Auth::getInstance()->requireAdmin();

        // Validate ID
        if (!Security::validateId($id)) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = 'ID inválido';
            header('Location: /manager/satisfaction-surveys');
            exit;
        }

        // Get survey data
        $survey = $this->db->fetchOne("SELECT * FROM satisfaction_surveys WHERE id = ?", [$id]);

        if (!$survey) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = 'Encuesta no encontrada';
            header('Location: /manager/satisfaction-surveys');
            exit;
        }

        try {
            // Generate Word document
            $filepath = WordGenerator::generateSurveyDocument($survey);
            
            // Set headers for download
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            header('Content-Disposition: attachment; filename="encuesta_satisfaccion_' . $id . '.docx"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            
            // Clear output buffer
            ob_clean();
            flush();
            
            // Read file and output
            readfile($filepath);
            
            // Delete temporary file
            unlink($filepath);
            
            exit;
        } catch (\Exception $e) {
            error_log("Error generating Word document: " . $e->getMessage());
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = 'Error al generar el documento Word';
            header('Location: /manager/satisfaction-surveys');
            exit;
        }
    }
}

