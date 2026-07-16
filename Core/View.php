<?php

namespace Core;

class View
{
    private $data = [];
    private $layout = null;

    public function render($view, $data = [], $layout = null)
    {
        $this->data = $data;
        $this->layout = $layout;

        extract($data);

        ob_start();
        
        $viewFile = $this->getViewPath($view);
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            throw new \Exception("View not found: {$view}");
        }

        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = $this->getViewPath($layout);
            if (file_exists($layoutFile)) {
                ob_start();
                include $layoutFile;
                $content = ob_get_clean();
            }
        }

        echo $content;
    }

    private function getViewPath($view)
    {
        $rootPath = Config::get('paths.root');
        
        // Check if absolute path
        if (file_exists($view)) {
            return $view;
        }

        // Try relative to root
        return $rootPath . '/' . $view . '.php';
    }

    public function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public function url($path = '')
    {
        return Config::get('site.url') . '/' . ltrim($path, '/');
    }
}
