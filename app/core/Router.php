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

        // Handle named parameters like {id}
        $pattern = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $pattern);

        // Add ^ and $ to ensure full match
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
        error_log("Attempting to match URL: " . $url . " with method: " . $method);

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $params) {
                error_log("Checking pattern: " . $pattern . " against URL: " . $url);

                if (preg_match($pattern, $url, $matches)) {
                    // Extract numeric parameters
                    $numericParams = [];
                    foreach ($matches as $key => $match) {
                        if (is_int($key) && $key > 0) {
                            $numericParams[] = $match;
                        }
                        if (is_string($key)) {
                            $params[$key] = $match;
                        }
                    }
                
                    // Store numeric params if there are any
                    if (!empty($numericParams)) {
                        $params['_numeric_params'] = $numericParams;
                    }
                
                    $this->params = $params;
                    return true;
                }
            }
        }

        error_log("No match found for URL: " . $url);
        return false;
    }

    public function dispatch($url)
    {
        $url = $this->cleanUrl($url);
        $method = $_SERVER['REQUEST_METHOD'];

        error_log("Attempting to dispatch URL: " . $url . " with method: " . $method);

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

                // Check if we have captured numeric parameters from URL
                $actionParams = [];
                if (isset($this->params['_numeric_params']) && !empty($this->params['_numeric_params'])) {
                    $actionParams = $this->params['_numeric_params'];
                    error_log("Using numeric params: " . print_r($actionParams, true));
                } else {
                    // Extract named parameters
                    $params = $this->params;
                    unset($params['controller'], $params['action'], $params['middleware'], $params['route'], $params['_numeric_params']);
                    $actionParams = array_values($params);
                    error_log("Using named params: " . print_r($actionParams, true));
                }

                error_log("Executing action: " . $action . " with parameters: " . print_r($actionParams, true));
                return call_user_func_array([$controllerObject, $action], $actionParams);
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
