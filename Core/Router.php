<?php

namespace Core;

class Router
{
    private $routes = [];
    private $namedRoutes = [];

    public function get($path, $callback, $name = null)
    {
        $this->addRoute('GET', $path, $callback, $name);
    }

    public function post($path, $callback, $name = null)
    {
        $this->addRoute('POST', $path, $callback, $name);
    }

    public function any($path, $callback, $name = null)
    {
        $this->addRoute('ANY', $path, $callback, $name);
    }

    private function addRoute($method, $path, $callback, $name = null)
    {
        $route = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];

        $this->routes[] = $route;

        if ($name) {
            $this->namedRoutes[$name] = $path;
        }
    }

    public function dispatch($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== 'ANY' && $route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                
                // Extract only numeric keys (positional parameters)
                $params = array_filter($matches, function($key) {
                    return is_int($key);
                }, ARRAY_FILTER_USE_KEY);
                
                // Re-index array to have sequential numeric keys
                $params = array_values($params);
                
                return $this->executeCallback($route['callback'], $params);
            }
        }

        http_response_code(404);
        echo "404 - Page Not Found";
        return;
    }

    private function convertToRegex($path)
    {
        // Convert :param to named capture groups
        $pattern = preg_replace('/\/:([a-zA-Z0-9_]+)/', '/(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function executeCallback($callback, $params)
    {
        if (is_callable($callback)) {
            return $callback(...$params);
        }

        if (is_string($callback)) {
            // Validate callback format
            if (strpos($callback, '@') === false) {
                error_log("ERROR: Invalid callback format: $callback (missing @)");
                throw new \Exception("Invalid route callback format: $callback");
            }

            list($controller, $method) = explode('@', $callback);
            
            // Check if class exists
            if (!class_exists($controller)) {
                error_log("ERROR: Controller class not found: $controller");
                error_log("Autoload paths: " . print_r(spl_autoload_functions(), true));
                throw new \Exception("Controller class not found: $controller");
            }

            $controllerInstance = new $controller();
            
            // Check if method exists
            if (!method_exists($controllerInstance, $method)) {
                error_log("ERROR: Method $method not found in controller $controller");
                error_log("Available methods: " . implode(', ', get_class_methods($controllerInstance)));
                throw new \Exception("Method $method not found in controller $controller");
            }

            return $controllerInstance->$method(...$params);
        }

        error_log("ERROR: Callback is neither callable nor string: " . print_r($callback, true));
        throw new \Exception("Invalid route callback type");
    }

    public function url($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            return '#';
        }

        $path = $this->namedRoutes[$name];
        
        foreach ($params as $key => $value) {
            $path = str_replace(":{$key}", $value, $path);
        }

        return Config::get('site.url') . $path;
    }
}
