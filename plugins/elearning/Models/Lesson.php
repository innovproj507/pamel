<?php

namespace Plugins\Elearning\Models;

use Core\Model;

class Lesson extends Model
{
    protected $table = 'lms_lessons';

    /**
     * Get completed lesson IDs for a student in a specific course.
     */
    public function completedByStudentInCourse($userId, $courseId)
    {
        $rows = $this->db->fetchAll(
            "SELECT lp.lesson_id 
             FROM lms_lesson_progress lp
             JOIN {$this->table} l ON l.id = lp.lesson_id
             WHERE lp.student_id = ? AND l.course_id = ?",
            [$userId, $courseId]
        );
        
        return array_column($rows, 'lesson_id');
    }

    /**
     * Get all lessons in a course.
     */
    public function byCourse($courseId)
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE course_id = ? ORDER BY order_num ASC",
            [$courseId]
        );
    }

    /**
     * Get previous lesson in course for sequential tracking.
     */
    public function previousInCourse($courseId, $orderNum)
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE course_id = ? AND order_num < ? AND is_active = 1 ORDER BY order_num DESC LIMIT 1",
            [$courseId, $orderNum]
        );
    }

    /**
     * Check if a lesson is completed by a student.
     */
    public function isCompleted($userId, $lessonId)
    {
        $result = $this->db->fetchOne(
            "SELECT id FROM lms_lesson_progress WHERE student_id = ? AND lesson_id = ? LIMIT 1",
            [$userId, $lessonId]
        );
        return $result !== false;
    }

    /**
     * Mark a lesson as completed for a student.
     */
    public function markCompleted($userId, $lessonId)
    {
        // Use INSERT IGNORE or check existence to prevent duplicates
        return $this->db->query(
            "INSERT IGNORE INTO lms_lesson_progress (student_id, lesson_id, completed_at) VALUES (?, ?, NOW())",
            [$userId, $lessonId]
        );
    }

    public function setActive(int $lessonId, bool $active): bool
    {
        return (bool) $this->db->update('lms_lessons', ['is_active' => (int) $active], 'id = :id', ['id' => $lessonId]);
    }

    public function deleteLesson(int $lessonId): bool
    {
        return (bool) $this->db->delete('lms_lessons', 'id = :id', ['id' => $lessonId]);
    }
}
