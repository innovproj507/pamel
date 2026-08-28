<?php

declare(strict_types=1);

namespace Plugins\Elearning\Controllers;

use Core\View;
use Plugins\Elearning\Models\Forum;

class AdminForumController extends BaseController
{
    private Forum $forumModel;

    public function __construct()
    {
        parent::__construct();
        $this->forumModel = new Forum();
    }

    public function index(): void
    {
        $this->requireCan('lms.forums.view');
        $forums = $this->forumModel->allWithCount();
        $view   = new View();
        $view->render('admin/views/lms/forums/index', compact('forums'), 'admin/views/layout');
    }

    public function store(): void
    {
        $this->requireCan('lms.forums.manage');
        $this->validateCsrf('/manager/lms/forums');

        $title = trim($_POST['title'] ?? '');
        if (empty($title)) {
            $this->redirect('/manager/lms/forums');
        }

        $slug = $this->forumModel->makeSlug($title);

        $existing = $this->db->fetchOne('SELECT id FROM lms_forums WHERE slug = ?', [$slug]);
        if ($existing) {
            $slug .= '-' . time();
        }

        $this->db->insert('lms_forums', [
            'title'       => $title,
            'description' => trim($_POST['description'] ?? ''),
            'slug'        => $slug,
            'order_num'   => (int) ($_POST['order_num'] ?? 0),
            'is_active'   => 1,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->redirect('/manager/lms/forums');
    }

    public function toggle($id): void
    {
        $this->requireCan('lms.forums.manage');
        $this->validateCsrf('/manager/lms/forums');

        $forum = $this->forumModel->find((int) $id);
        if ($forum) {
            $this->db->update('forums', ['is_active' => $forum['is_active'] ? 0 : 1], 'id = :id', ['id' => (int) $id]);
        }

        $this->redirect('/manager/lms/forums');
    }

    public function delete($id): void
    {
        $this->requireCan('lms.forums.manage');
        $this->validateCsrf('/manager/lms/forums');

        $topics = $this->db->fetchAll('SELECT id FROM lms_forum_topics WHERE forum_id = ?', [(int) $id]);
        foreach ($topics as $topic) {
            $this->db->delete('lms_forum_posts', 'topic_id = :tid', ['tid' => $topic['id']]);
        }
        $this->db->delete('lms_forum_topics', 'forum_id = :fid', ['fid' => (int) $id]);
        $this->db->delete('lms_forums', 'id = :id', ['id' => (int) $id]);

        $this->redirect('/manager/lms/forums');
    }

    public function deleteTopic($id): void
    {
        $this->requireCan('lms.forums.manage');
        $this->validateCsrf('/manager/lms/forums');
        $this->forumModel->deleteTopic((int) $id);
        $this->redirect('/manager/lms/forums');
    }

    public function deletePost($id): void
    {
        $this->requireCan('lms.forums.manage');
        $this->validateCsrf('/manager/lms/forums');
        $this->forumModel->deletePost((int) $id);
        $this->redirect('/manager/lms/forums');
    }
}
