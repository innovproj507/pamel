<?php

namespace Plugins\Elearning\Models;

use Core\Model;

class Enrollment extends Model
{
    protected $table = 'lms_enrollments';

    /**
     * Check if a student is enrolled in a course.
     */
    public function isEnrolled($studentId, $courseId)
    {
        $result = $this->db->fetchOne(
            "SELECT id FROM {$this->table} WHERE student_id = ? AND course_id = ? LIMIT 1",
            [$studentId, $courseId]
        );
        return $result !== false && $result !== null;
    }

    /**
     * Enroll a student in a course.
     */
    public function enroll($studentId, $courseId)
    {
        return $this->create([
            'student_id' => $studentId,
            'course_id'  => $courseId,
            'status'     => 'active',
        ]);
    }

    /**
     * Get enrollments for a user with course and progress details.
     */
    public function getByStudent($studentId)
    {
        return $this->db->fetchAll(
            "SELECT e.*, c.title, c.slug, c.image, u.name as teacher_name,
                    (SELECT COUNT(*) FROM lms_lessons WHERE course_id = c.id AND is_active = 1) as total_lessons,
                    (SELECT COUNT(*) FROM lms_lesson_progress lp
                     JOIN lms_lessons l ON l.id = lp.lesson_id
                     WHERE lp.student_id = e.student_id AND l.course_id = c.id) as completed_lessons
             FROM {$this->table} e
             JOIN lms_courses c ON c.id = e.course_id
             LEFT JOIN users u ON u.id = c.teacher_id
             WHERE e.student_id = ?
             ORDER BY e.enrolled_at DESC",
            [$studentId]
        );
    }
}
