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
        \Core\Auth::getInstance()->requireCan('settings.plugins', '/manager/login');

        $pluginManager = PluginManager::getInstance();
        $loadedPlugins = $pluginManager->getLoadedPlugins();

        $this->view->render('admin/views/plugins', [
            'title' => 'Plugins',
            'plugins' => $loadedPlugins
        ], 'admin/views/layout');
    }
}
