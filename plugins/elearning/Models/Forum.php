<?php

declare(strict_types=1);

namespace Plugins\Elearning\Models;

use Core\Model;

class Forum extends Model
{
    protected $table = 'lms_forums';

    public function allWithCount(): array
    {
        return $this->db->fetchAll(
            'SELECT f.*, COUNT(t.id) AS topics_count
             FROM lms_forums f
             LEFT JOIN lms_forum_topics t ON t.forum_id = f.id
             GROUP BY f.id
             ORDER BY f.order_num ASC, f.created_at ASC'
        );
    }

    public function allActiveWithCount(): array
    {
        return $this->db->fetchAll(
            'SELECT f.*, COUNT(t.id) AS topics_count
             FROM lms_forums f
             LEFT JOIN lms_forum_topics t ON t.forum_id = f.id
             WHERE f.is_active = 1
             GROUP BY f.id
             ORDER BY f.order_num ASC, f.created_at ASC'
        );
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetchOne('SELECT * FROM lms_forums WHERE slug = ? AND is_active = 1', [$slug]) ?: null;
    }

    public function topicsByForum(int $forumId): array
    {
        return $this->db->fetchAll(
            'SELECT t.*, COUNT(p.id) AS replies_count
             FROM lms_forum_topics t
             LEFT JOIN lms_forum_posts p ON p.topic_id = t.id
             WHERE t.forum_id = ?
             GROUP BY t.id
             ORDER BY t.is_pinned DESC, t.created_at DESC',
            [$forumId]
        );
    }

    public function findTopic(int $topicId): ?array
    {
        return $this->db->fetchOne('SELECT * FROM lms_forum_topics WHERE id = ?', [$topicId]) ?: null;
    }

    public function postsByTopic(int $topicId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM lms_forum_posts WHERE topic_id = ? ORDER BY created_at ASC',
            [$topicId]
        );
    }

    public function createTopic(array $data): int|false
    {
        $id = $this->db->insert('lms_forum_topics', $data);
        return $id !== false ? (int) $id : false;
    }

    public function createPost(array $data): int|false
    {
        $id = $this->db->insert('lms_forum_posts', $data);
        return $id !== false ? (int) $id : false;
    }

    public function deleteTopic(int $id): bool
    {
        $this->db->delete('lms_forum_posts', 'topic_id = :tid', ['tid' => $id]);
        return (bool) $this->db->delete('lms_forum_topics', 'id = :id', ['id' => $id]);
    }

    public function deletePost(int $id): bool
    {
        return (bool) $this->db->delete('lms_forum_posts', 'id = :id', ['id' => $id]);
    }

    public function makeSlug(string $text): string
    {
        $map = ['á'=>'a','à'=>'a','ä'=>'a','â'=>'a','é'=>'e','è'=>'e','ë'=>'e','ê'=>'e',
                'í'=>'i','ì'=>'i','ï'=>'i','î'=>'i','ó'=>'o','ò'=>'o','ö'=>'o','ô'=>'o',
                'ú'=>'u','ù'=>'u','ü'=>'u','û'=>'u','ñ'=>'n','ç'=>'c'];
        $text = mb_strtolower(strtr($text, $map));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        return preg_replace('/[\s-]+/', '-', trim($text));
    }
}
