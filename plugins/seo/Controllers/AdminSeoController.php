<?php

namespace Plugins\Seo\Controllers;

use Core\Controller;
use Core\Auth;
use Plugins\Seo\Models\MetaTag;

class AdminSeoController extends Controller
{
    private $metaTagModel;

    public function __construct()
    {
        parent::__construct();
        $this->metaTagModel = new MetaTag();
    }

    public function index()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $metaTags = $this->metaTagModel->getAllPages();
        
        // Get robots.txt content
        $robotsTxt = $this->db->fetchOne("SELECT setting_value FROM settings WHERE setting_key = 'robots_txt'");

        $this->view->render('plugins/seo/views/admin/seo-settings', [
            'title' => 'SEO Settings',
            'metaTags' => $metaTags,
            'robotsTxt' => $robotsTxt ? $robotsTxt['setting_value'] : ''
        ], 'admin/views/layout');
    }

    public function saveMeta()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->metaTagModel->upsert($_POST['page_url'], [
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'keywords' => $_POST['keywords'] ?? '',
                'og_title' => $_POST['og_title'] ?? '',
                'og_description' => $_POST['og_description'] ?? '',
                'og_image' => $_POST['og_image'] ?? ''
            ]);

            $this->redirect('/manager/seo');
        }
    }

    public function saveRobots()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $existing = $this->db->fetchOne("SELECT * FROM settings WHERE setting_key = 'robots_txt'");
            
            if ($existing) {
                $this->db->update('settings', 
                    ['setting_value' => $_POST['robots_txt']], 
                    'setting_key = :key', 
                    ['key' => 'robots_txt']
                );
            } else {
                $this->db->insert('settings', [
                    'setting_key' => 'robots_txt',
                    'setting_value' => $_POST['robots_txt']
                ]);
            }

            $this->redirect('/manager/seo');
        }
    }
}
