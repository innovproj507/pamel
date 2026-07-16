<?php

namespace Core;

class PluginManager
{
    private $plugins = [];
    private $hooks = [];
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function loadPlugins()
    {
        $enabledPlugins = Config::get('plugins', []);
        $pluginsPath = Config::get('paths.plugins');

        foreach ($enabledPlugins as $pluginName) {
            $pluginFile = $pluginsPath . '/' . $pluginName . '/' . $pluginName . '.php';
            
            if (file_exists($pluginFile)) {
                require_once $pluginFile;
                $this->plugins[$pluginName] = [
                    'name' => $pluginName,
                    'path' => $pluginFile,
                    'loaded' => true
                ];
            }
        }
    }

    public function registerHook($hookName, $callback, $priority = 10)
    {
        if (!isset($this->hooks[$hookName])) {
            $this->hooks[$hookName] = [];
        }

        $this->hooks[$hookName][] = [
            'callback' => $callback,
            'priority' => $priority
        ];

        // Sort by priority
        usort($this->hooks[$hookName], function($a, $b) {
            return $a['priority'] - $b['priority'];
        });
    }

    public function executeHook($hookName, $data = null)
    {
        if (!isset($this->hooks[$hookName])) {
            return $data;
        }

        foreach ($this->hooks[$hookName] as $hook) {
            if (is_callable($hook['callback'])) {
                $data = call_user_func($hook['callback'], $data);
            }
        }

        return $data;
    }

    public function addFilter($filterName, $callback, $priority = 10)
    {
        $this->registerHook($filterName, $callback, $priority);
    }

    public function applyFilter($filterName, $value)
    {
        return $this->executeHook($filterName, $value);
    }

    public function addAction($actionName, $callback, $priority = 10)
    {
        $this->registerHook($actionName, $callback, $priority);
    }

    public function doAction($actionName, $data = null)
    {
        $this->executeHook($actionName, $data);
    }

    public function getLoadedPlugins()
    {
        return $this->plugins;
    }

    public function isPluginLoaded($pluginName)
    {
        return isset($this->plugins[$pluginName]) && $this->plugins[$pluginName]['loaded'];
    }
}
