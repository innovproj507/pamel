<?php

namespace Admin\Controllers;

use Core\Controller;
use Core\Auth;
use Core\PluginManager;

class PluginController extends Controller
{
    public function index()
    {
        $auth = Auth::getInstance();
        $auth->requireAdmin('/manager/login');

        $pluginManager = PluginManager::getInstance();
        $loadedPlugins = $pluginManager->getLoadedPlugins();

        $this->view->render('admin/views/plugins', [
            'title' => 'Plugins',
            'plugins' => $loadedPlugins
        ], 'admin/views/layout');
    }
}
