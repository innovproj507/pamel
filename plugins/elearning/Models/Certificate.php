<?php

namespace Plugins\Elearning\Models;

use Core\Database;

class Certificate
{
    private $db;
    private $table = 'lms_certificates';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get certificate by student and course.
     */
    public function getForStudentCourse($studentId, $courseId)
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE student_id = ? AND course_id = ?",
            [$studentId, $courseId]
        );
    }

    /**
     * Create a new certificate record.
     */
    public function issue($studentId, $courseId, $code)
    {
        $data = [
            'student_id'       => $studentId,
            'course_id'        => $courseId,
            'certificate_code' => $code,
            'issued_at'        => date('Y-m-d H:i:s')
        ];

        return $this->db->insert($this->table, $data);
    }

    /**
     * Generate a unique certificate code.
     */
    public function generateCode()
    {
        return 'PAMEL-' . strtoupper(uniqid());
    }
}
