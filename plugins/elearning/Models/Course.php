<?php

namespace Plugins\Elearning\Models;

use Core\Model;

class Course extends Model
{
    protected $table = 'lms_courses';

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_ARCHIVED  = 'archived';

    // Columns pulled from linked product when present
    private const PRODUCT_COLS = "
        p.id            AS product_id,
        p.name          AS product_name,
        p.status        AS product_status,
        COALESCE(p.price, c.price)  AS price,
        p.course_code               AS course_code,
        p.imo_model_course_no,
        p.duration_hours,
        p.modality,
        p.renewal_price,
        COALESCE(p.image, c.image)          AS image
    ";

    /**
     * Visibility rule: published + (no linked product OR linked product is active).
     */
    private function visibilityWhere(): string
    {
        return "c.status = 'published' AND (c.product_id IS NULL OR p.status = 'active')";
    }

    /**
     * GET /api/v1/courses — paginated list of visible courses.
     */
    public function published($page = 1, $perPage = 12, $categoryId = null)
    {
        $where  = $this->visibilityWhere();
        $params = [];

        if ($categoryId) {
            $where   .= " AND c.category_id = ?";
            $params[] = $categoryId;
        }

        $offset = ($page - 1) * $perPage;

        $totalResult = $this->db->fetchOne(
            "SELECT COUNT(*) AS t
             FROM {$this->table} c
             LEFT JOIN products p ON p.id = c.product_id
             WHERE {$where}",
            $params
        );
        $total = (int) ($totalResult['t'] ?? 0);

        $data = $this->db->fetchAll(
            "SELECT c.*,
                    " . self::PRODUCT_COLS . ",
                    u.name  AS teacher_name,
                    cat.name AS category_name,
                    (SELECT COUNT(*) FROM lms_lessons WHERE course_id = c.id) AS lesson_count,
                    (SELECT COUNT(*) FROM lms_quizzes WHERE course_id = c.id) AS quiz_count
             FROM {$this->table} c
             LEFT JOIN products p        ON p.id   = c.product_id
             LEFT JOIN users u           ON u.id   = c.teacher_id
             LEFT JOIN lms_categories cat ON cat.id = c.category_id
             WHERE {$where}
             ORDER BY c.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data'         => $data,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Find a single course by ID (only if visible).
     */
    public function find($id)
    {
        return $this->db->fetchOne(
            "SELECT c.*,
                    " . self::PRODUCT_COLS . ",
                    u.name    AS teacher_name,
                    u.bio     AS teacher_bio,
                    cat.name  AS category_name
             FROM {$this->table} c
             LEFT JOIN products p        ON p.id   = c.product_id
             LEFT JOIN users u           ON u.id   = c.teacher_id
             LEFT JOIN lms_categories cat ON cat.id = c.category_id
             WHERE c.id = ?",
            [$id]
        );
    }

    /**
     * Find a course by its slug (only if visible).
     */
    public function findBySlug($slug)
    {
        return $this->db->fetchOne(
            "SELECT c.*,
                    " . self::PRODUCT_COLS . ",
                    u.name    AS teacher_name,
                    u.bio     AS teacher_bio,
                    cat.name  AS category_name
             FROM {$this->table} c
             LEFT JOIN products p        ON p.id   = c.product_id
             LEFT JOIN users u           ON u.id   = c.teacher_id
             LEFT JOIN lms_categories cat ON cat.id = c.category_id
             WHERE c.slug = ? AND ({$this->visibilityWhere()})",
            [$slug]
        );
    }

    /**
     * Get lessons and quizzes for a course.
     */
    public function getLessonsAndQuizzes($courseId)
    {
        $lessons = $this->db->fetchAll(
            "SELECT * FROM lms_lessons WHERE course_id = ? ORDER BY order_num ASC",
            [$courseId]
        );

        $quizzes = $this->db->fetchAll(
            "SELECT * FROM lms_quizzes WHERE course_id = ? AND (lesson_id IS NULL OR lesson_id = 0) ORDER BY created_at ASC",
            [$courseId]
        );

        // Lesson-level quizzes with questions+options, keyed by lesson_id
        $rawLessonQuizzes = $this->db->fetchAll(
            "SELECT * FROM lms_quizzes WHERE course_id = ? AND lesson_id > 0 ORDER BY created_at ASC",
            [$courseId]
        );
        $lessonQuizzes = [];
        foreach ($rawLessonQuizzes as &$lq) {
            $qs = $this->db->fetchAll(
                "SELECT * FROM lms_questions WHERE quiz_id = ? ORDER BY order_num ASC, id ASC",
                [$lq['id']]
            );
            foreach ($qs as &$q) {
                $q['options'] = $this->db->fetchAll(
                    "SELECT * FROM lms_question_options WHERE question_id = ? ORDER BY order_num ASC",
                    [$q['id']]
                );
            }
            $lq['questions']                 = $qs;
            $lessonQuizzes[$lq['lesson_id']] = $lq;
        }

        return ['lessons' => $lessons, 'quizzes' => $quizzes, 'lesson_quizzes' => $lessonQuizzes];
    }

    /**
     * Count visible (published + product active) courses.
     */
    public function countPublished()
    {
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) AS total
             FROM {$this->table} c
             LEFT JOIN products p ON p.id = c.product_id
             WHERE {$this->visibilityWhere()}"
        );
        return $result ? (int) $result['total'] : 0;
    }
}
