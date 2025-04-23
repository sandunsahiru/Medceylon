<?php

namespace App\Core;

class Router
{
    private $routes = [];
    private $params = [];
    private $notFoundCallback;
    private $basePath = '/Medceylon';

    public function add($method, $route, $controller, $action, $middleware = null)
    {
        $pattern = preg_replace('/\//', '\\/', $route);
        $pattern = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $pattern);
        $pattern = '/^' . $pattern . '$/i';

        $this->routes[$method][$pattern] = [
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware,
            'route' => $route
        ];
    }

    public function get($route, $controller, $action, $middleware = null)
    {
        $this->add('GET', $route, $controller, $action, $middleware);
    }

    public function post($route, $controller, $action, $middleware = null)
    {
        $this->add('POST', $route, $controller, $action, $middleware);
    }

    public function bus($route, $controller, $action, $middleware = null)
    {
        $this->add('BUS', $route, $controller, $action, $middleware);
    }

    public function setNotFound($callback)
    {
        $this->notFoundCallback = $callback;
    }

    private function match($url, $method)
    {
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $params) {
                if (preg_match($pattern, $url, $matches)) {
                    foreach ($matches as $key => $match) {
                        if (is_string($key)) {
                            $params[$key] = $match;
                        }
                    }
                    $this->params = $params;
                    return true;
                }
            }
        }
        return false;
    }

    public function dispatch($url)
    {
        $url = $this->cleanUrl($url);
        $method = $_SERVER['REQUEST_METHOD'];

        if ($this->match($url, $method)) {
            try {
                error_log("Matched URL: " . $url);
                error_log("Controller: " . $this->params['controller']);
                error_log("Action: " . $this->params['action']);

                $controller = "App\\Controllers\\" . $this->params['controller'];

                if (!class_exists($controller)) {
                    throw new \Exception("Controller {$controller} not found");
                }

                $controllerObject = new $controller();
                $action = $this->params['action'];

                if (!is_callable([$controllerObject, $action])) {
                    throw new \Exception("Action {$action} not found in {$controller}");
                }

                if (isset($this->params['middleware'])) {
                    error_log("Executing middleware: " . $this->params['middleware']);
                    $middleware = new $this->params['middleware']();
                    if (!$middleware->handle()) {
                        error_log("Middleware check failed");
                        return false;
                    }
                }

                // ✅ NEW — pass dynamic params like {id}
                $params = $this->params;
                unset($params['controller'], $params['action'], $params['middleware'], $params['route']);

                error_log("Executing action: " . $action);
                return call_user_func_array([$controllerObject, $action], $params);
            } catch (\Exception $e) {
                error_log("Error in dispatch: " . $e->getMessage());
                throw $e;
            }
        } else {
            error_log("No route match found for URL: " . $url);
        }

        $this->handleNotFound();
    }

    private function cleanUrl($url)
    {
        $parsedUrl = parse_url($url, PHP_URL_PATH);
        $parsedUrl = substr($parsedUrl, strlen($this->basePath));
        $parsedUrl = trim($parsedUrl, '/');
        return empty($parsedUrl) ? '/' : '/' . $parsedUrl;
    }

    private function handleNotFound()
    {
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            header("HTTP/1.0 404 Not Found");
            $errorPage = '../app/views/errors/404.php';

            if (file_exists($errorPage)) {
                require $errorPage;
            } else {
                echo '404 Not Found';
            }
        }
    }

    public function url($path)
    {
        return $this->basePath . '/' . ltrim($path, '/');
    }
}
