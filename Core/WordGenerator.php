<?php

namespace Core;

use PhpOffice\PhpWord\TemplateProcessor;

class WordGenerator
{
    /**
     * Generate Word document for satisfaction survey using existing template
     * @param array $surveyData Survey data from database
     * @return string Path to generated file
     */
    public static function generateSurveyDocument($surveyData)
    {
        $templatePath = __DIR__ . '/../public/assets/documents/F-20 Encuesta de satisfacción (ES).docx';
        
        // Check if template exists
        if (!file_exists($templatePath)) {
            error_log("Template not found at: " . $templatePath);
            // Fallback to generating document from scratch if template not found
            return self::generateFromScratch($surveyData);
        }
        
        try {
            // Load template
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Replace placeholders with survey data
            $templateProcessor->setValue('first_name', $surveyData['first_name']);
            $templateProcessor->setValue('last_name', $surveyData['last_name']);
            $templateProcessor->setValue('email', $surveyData['email']);
            $templateProcessor->setValue('course_name', $surveyData['course_name']);
            $templateProcessor->setValue('survey_date', date('d/m/Y', strtotime($surveyData['survey_date'])));
            
            // Staff attention rating
            $templateProcessor->setValue('staff_attention_rating', $surveyData['staff_attention_rating']);
            $templateProcessor->setValue('staff_attention_comments', 
                !empty($surveyData['staff_attention_comments']) ? $surveyData['staff_attention_comments'] : 'N/A');
            
            // Mark checkboxes for staff attention
            $templateProcessor->setValue('staff_check_a', strpos($surveyData['staff_attention_rating'], 'A-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('staff_check_b', strpos($surveyData['staff_attention_rating'], 'B-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('staff_check_c', strpos($surveyData['staff_attention_rating'], 'C-') === 0 ? '☑' : '☐');
            
            // Training quality rating
            $templateProcessor->setValue('training_quality_rating', $surveyData['training_quality_rating']);
            $templateProcessor->setValue('training_quality_comments', 
                !empty($surveyData['training_quality_comments']) ? $surveyData['training_quality_comments'] : 'N/A');
            
            // Mark checkboxes for training quality
            $templateProcessor->setValue('training_check_a', strpos($surveyData['training_quality_rating'], 'A-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('training_check_b', strpos($surveyData['training_quality_rating'], 'B-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('training_check_c', strpos($surveyData['training_quality_rating'], 'C-') === 0 ? '☑' : '☐');
            
            // Instructor performance rating
            $templateProcessor->setValue('instructor_performance_rating', $surveyData['instructor_performance_rating']);
            $templateProcessor->setValue('instructor_performance_comments', 
                !empty($surveyData['instructor_performance_comments']) ? $surveyData['instructor_performance_comments'] : 'N/A');
            
            // Mark checkboxes for instructor performance
            $templateProcessor->setValue('instructor_check_a', strpos($surveyData['instructor_performance_rating'], 'A-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('instructor_check_b', strpos($surveyData['instructor_performance_rating'], 'B-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('instructor_check_c', strpos($surveyData['instructor_performance_rating'], 'C-') === 0 ? '☑' : '☐');
            
            // Infrastructure rating
            $templateProcessor->setValue('infrastructure_rating', $surveyData['infrastructure_rating']);
            $templateProcessor->setValue('infrastructure_comments', 
                !empty($surveyData['infrastructure_comments']) ? $surveyData['infrastructure_comments'] : 'N/A');
            
            // Mark checkboxes for infrastructure
            $templateProcessor->setValue('infrastructure_check_a', strpos($surveyData['infrastructure_rating'], 'A-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('infrastructure_check_b', strpos($surveyData['infrastructure_rating'], 'B-') === 0 ? '☑' : '☐');
            $templateProcessor->setValue('infrastructure_check_c', strpos($surveyData['infrastructure_rating'], 'C-') === 0 ? '☑' : '☐');
            
            // Additional fields that might be in template
            $templateProcessor->setValue('student_name', $surveyData['first_name'] . ' ' . $surveyData['last_name']);
            $templateProcessor->setValue('date', date('d/m/Y'));
            $templateProcessor->setValue('current_date', date('d/m/Y'));
            
            // Save to temporary file
            $surveyId = $surveyData['id'] ?? 'temp';
            $filename = 'encuesta_satisfaccion_' . $surveyId . '_' . time() . '.docx';
            $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
            
            $templateProcessor->saveAs($filepath);
            
            return $filepath;
            
        } catch (\Exception $e) {
            error_log("Error using template: " . $e->getMessage());
            // Fallback to generating from scratch
            return self::generateFromScratch($surveyData);
        }
    }
    
    /**
     * Fallback method: Generate document from scratch if template fails
     */
    private static function generateFromScratch($surveyData)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        
        // Set document properties
        $properties = $phpWord->getDocInfo();
        $properties->setCreator('GCL International');
        $properties->setCompany('GCL International');
        $properties->setTitle('Encuesta de Satisfacción');
        
        $section = $phpWord->addSection();
        
        // Title
        $section->addText(
            'ENCUESTA DE SATISFACCIÓN',
            ['bold' => true, 'size' => 18, 'color' => '0066cc'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        
        $section->addTextBreak(1);
        
        $section->addText(
            'GCL International',
            ['bold' => true, 'size' => 14],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        
        $section->addTextBreak(2);
        
        // Student Information
        $section->addText('DATOS DEL ESTUDIANTE', ['bold' => true, 'size' => 14, 'color' => '0066cc']);
        $section->addTextBreak(1);
        
        self::addField($section, 'Nombre:', $surveyData['first_name'] . ' ' . $surveyData['last_name']);
        self::addField($section, 'Email:', $surveyData['email']);
        self::addField($section, 'Curso:', $surveyData['course_name']);
        self::addField($section, 'Fecha:', $surveyData['survey_date']);
        
        $section->addTextBreak(2);
        
        // Ratings
        $section->addText('CALIFICACIONES', ['bold' => true, 'size' => 14, 'color' => '0066cc']);
        $section->addTextBreak(1);
        
        $section->addText('1. Atención del Personal', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(0.5);
        self::addRating($section, $surveyData['staff_attention_rating']);
        if (!empty($surveyData['staff_attention_comments'])) {
            $section->addText('Comentarios:', ['italic' => true]);
            $section->addText($surveyData['staff_attention_comments'], ['size' => 10]);
        }
        $section->addTextBreak(1);
        
        $section->addText('2. Calidad del Entrenamiento', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(0.5);
        self::addRating($section, $surveyData['training_quality_rating']);
        if (!empty($surveyData['training_quality_comments'])) {
            $section->addText('Comentarios:', ['italic' => true]);
            $section->addText($surveyData['training_quality_comments'], ['size' => 10]);
        }
        $section->addTextBreak(1);
        
        $section->addText('3. Desempeño del Instructor', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(0.5);
        self::addRating($section, $surveyData['instructor_performance_rating']);
        if (!empty($surveyData['instructor_performance_comments'])) {
            $section->addText('Comentarios:', ['italic' => true]);
            $section->addText($surveyData['instructor_performance_comments'], ['size' => 10]);
        }
        $section->addTextBreak(1);
        
        // Infrastructure
        $section->addText('4. Infraestructura, Equipos y Simulador', ['bold' => true, 'size' => 12]);
        $section->addTextBreak(0.5);
        self::addRating($section, $surveyData['infrastructure_rating']);
        if (!empty($surveyData['infrastructure_comments'])) {
            $section->addText('Comentarios:', ['italic' => true]);
            $section->addText($surveyData['infrastructure_comments'], ['size' => 10]);
        }
        
        $section->addTextBreak(2);
        
        $section->addText(
            'Documento generado automáticamente el ' . date('d/m/Y H:i:s'),
            ['size' => 9, 'color' => '666666'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );
        
        $surveyId = $surveyData['id'] ?? 'temp';
        $filename = 'encuesta_satisfaccion_' . $surveyId . '_' . time() . '.docx';
        $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
        
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filepath);
        
        return $filepath;
    }
    
    private static function addField($section, $label, $value)
    {
        $section->addText($label, ['bold' => true]);
        $section->addText($value, ['size' => 11]);
        $section->addTextBreak(0.5);
    }
    
    private static function addRating($section, $rating)
    {
        $color = '000000';
        if (strpos($rating, 'A-') === 0) {
            $color = '4CAF50';
        } elseif (strpos($rating, 'B-') === 0) {
            $color = 'FF9800';
        } elseif (strpos($rating, 'C-') === 0) {
            $color = 'f44336';
        }
        
        $section->addText(
            'Calificación: ' . $rating,
            ['bold' => true, 'size' => 12, 'color' => $color]
        );
    }

    /**
     * Generate Word document for admission request using F-46 template
     * @param array $data Admission request data from database
     * @return string Path to generated file
     */
    public static function generateAdmissionDocument($data)
    {
        $templatePath = __DIR__ . '/../public/assets/documents/F-46 Solicitud y admisión de curso (ES).docx';

        if (!file_exists($templatePath)) {
            return self::generateAdmissionFromScratch($data);
        }

        try {
            $tp = new TemplateProcessor($templatePath);

            // ── Personal fields ────────────────────────────────────────────
            $tp->setValue('given_name',       $data['given_name']       ?? '');
            $tp->setValue('surname',          $data['surname']          ?? '');
            $tp->setValue('passport_id',      $data['passport_id']      ?? '');
            $tp->setValue('nationality',      $data['nationality']      ?? '');
            $tp->setValue('date_of_birth',    $data['date_of_birth']    ?? '');
            $tp->setValue('country_of_birth', $data['country_of_birth'] ?? '');
            $tp->setValue('email',            $data['email']            ?? '');
            $tp->setValue('phone',            $data['phone']            ?? '');
            $tp->setValue('address',          $data['address']          ?? '');
            $tp->setValue('date',             date('d/m/Y'));
            $tp->setValue('current_date',     date('d/m/Y'));
            $tp->setValue('terms_consent',    '☑ Doy mi consentimiento y acepto los términos.');

            // ── Detect how many course_check_N variables exist ─────────────
            $allVars   = $tp->getVariables();
            $checkVars = array_values(array_unique(array_filter($allVars, fn($v) => str_starts_with($v, 'course_check_'))));
            natsort($checkVars); // natural sort: course_check_1, course_check_2 ...
            $checkVars = array_values($checkVars);
            $total     = count($checkVars);

            // ── Get ordered course list from DB ────────────────────────────
            $courseNames = [];
            try {
                $db   = \Core\Database::getInstance();
                $rows = $db->fetchAll("SELECT name FROM products ORDER BY name");
                foreach ($rows as $r) $courseNames[] = $r['name'];
            } catch (\Exception $e) {
                // fallback if DB fails — leave checkboxes empty
            }

            // ── Fill checkboxes ────────────────────────────────────────────
            $selected = trim($data['course'] ?? '');
            for ($i = 0; $i < $total; $i++) {
                $courseName = $courseNames[$i] ?? '';
                $check      = (strtolower($courseName) === strtolower($selected)) ? '☑' : '☐';
                $tp->setValue($checkVars[$i], $check);
            }

            if (!empty($allVars)) {
                $filename = 'solicitud_admision_' . ($data['id'] ?? 'temp') . '_' . time() . '.docx';
                $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;
                $tp->saveAs($filepath);
                return $filepath;
            }
        } catch (\Exception $e) {
            error_log("Admission template error: " . $e->getMessage());
        }

        return self::generateAdmissionFromScratch($data);
    }

    /**
     * Fallback: generate admission document from scratch
     */
    private static function generateAdmissionFromScratch($data)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        // Page setup
        $section = $phpWord->addSection([
            'marginTop'    => 720,
            'marginBottom' => 720,
            'marginLeft'   => 1080,
            'marginRight'  => 1080,
        ]);

        // ── Header ──────────────────────────────────────────────────────────
        $section->addText('PANAMA MARITIME E-LEARNING (PAMEL), S.A.',
            ['bold' => true, 'size' => 14],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('QUALITY MANAGEMENT SYSTEM / SISTEMA DE GESTIÓN DE CALIDAD',
            ['size' => 9],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('FORMULARIO DE SOLICITUD Y ADMISIÓN DE CURSO',
            ['bold' => true, 'size' => 12, 'color' => '006699'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addText('F-46 / V.00',
            ['size' => 9, 'color' => '888888'],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        $section->addTextBreak(1);

        // ── Applicant data ──────────────────────────────────────────────────
        $section->addText('APPLICANT INFORMATION / DATOS DEL SOLICITANTE',
            ['bold' => true, 'size' => 11, 'color' => '006699']);
        $section->addTextBreak(1);

        $fields = [
            'GIVEN NAME / NOMBRE'                  => $data['given_name']       ?? '—',
            'SURNAME / APELLIDO'                   => $data['surname']          ?? '—',
            'PASSPORT or ID / PASAPORTE o CÉDULA'  => $data['passport_id']      ?? '—',
            'NATIONALITY / NACIONALIDAD'           => $data['nationality']      ?? '—',
            'DATE OF BIRTH / FECHA DE NACIMIENTO'  => $data['date_of_birth']    ?? '—',
            'COUNTRY OF BIRTH / PAÍS DE NACIMIENTO'=> $data['country_of_birth'] ?? '—',
            'E-MAIL / CORREO'                      => $data['email']            ?? '—',
            'PHONE / TELÉFONO'                     => $data['phone']            ?? '—',
            'ADDRESS / DIRECCIÓN'                  => $data['address']          ?? '—',
        ];

        foreach ($fields as $label => $value) {
            self::addField($section, $label . ':', $value);
        }

        $section->addTextBreak(1);

        // ── Course selection (checkboxes) ───────────────────────────────────
        $section->addText('DESCRIPTION OF THE COURSE / DESCRIPCIÓN DEL CURSO',
            ['bold' => true, 'size' => 11, 'color' => '006699']);
        $section->addTextBreak(1);

        // Get course list from DB; fall back to static list
        $selectedCourse = $data['course'] ?? '';
        $courses = [];

        try {
            $db = \Core\Database::getInstance();
            $rows = $db->fetchAll("SELECT name FROM products ORDER BY name");
            foreach ($rows as $row) {
                $courses[] = $row['name'];
            }
        } catch (\Exception $e) {
            // Static fallback
            $courses = [
                'Formación Básica para Operaciones de Carga en Petroleros y Quimiqueros/B',
                'Personal Safety and Social Responsibilities',
                'Safety Training For Personnel',
                'Passenger Ship Crowd Management',
                'Use of Leadership and Managerial Skills',
            ];
        }

        foreach ($courses as $courseName) {
            $check = (trim($courseName) === trim($selectedCourse)) ? '☑' : '☐';
            $section->addText(
                $check . '  ' . $courseName,
                ['size' => 10, 'bold' => (trim($courseName) === trim($selectedCourse))]
            );
        }

        $section->addTextBreak(2);

        // ── Consent ─────────────────────────────────────────────────────────
        $section->addText('CONSENT / CONSENTIMIENTO',
            ['bold' => true, 'size' => 11, 'color' => '006699']);
        $section->addTextBreak(1);
        
        $section->addText('☑ Doy mi consentimiento y acepto los términos.',
            ['size' => 10, 'bold' => true, 'color' => '008000']);
        $section->addText('Al enviar la solicitud, el candidato certificó que la información proporcionada es veraz, reemplazando la firma física.',
            ['size' => 9, 'italic' => true, 'color' => '666666']);

        $section->addTextBreak(2);

        // ── Footer ──────────────────────────────────────────────────────────
        $section->addText(
            'Fecha / Date: ' . date('d/m/Y') . '      |      Status: ' . ucfirst($data['status'] ?? ''),
            ['size' => 9, 'color' => '888888']
        );
        $section->addText(
            'Documento generado automáticamente / Automatically generated document',
            ['size' => 8, 'color' => 'aaaaaa', 'italic' => true],
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
        );

        $filename = 'solicitud_admision_' . ($data['id'] ?? 'temp') . '_' . time() . '.docx';
        $filepath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($filepath);

        return $filepath;
    }
}
