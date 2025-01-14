<?php
namespace App\Core;

class Router {
   private $routes = [];
   private $params = [];
   private $notFoundCallback;
   private $basePath = '/Medceylon';

   public function add($method, $route, $controller, $action, $middleware = null) {
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

   public function get($route, $controller, $action, $middleware = null) {
       $this->add('GET', $route, $controller, $action, $middleware);
   }

   public function post($route, $controller, $action, $middleware = null) {
       $this->add('POST', $route, $controller, $action, $middleware);
   }

   public function setNotFound($callback) {
       $this->notFoundCallback = $callback;
   }

   private function match($url, $method) {
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

   public function dispatch($url) {
       // Check if URL starts with basePath
       if (strpos($url, $this->basePath) !== 0) {
           header("Location: {$this->basePath}{$url}");
           exit();
       }

       $url = $this->cleanUrl($url);
       $method = $_SERVER['REQUEST_METHOD'];

       if ($this->match($url, $method)) {
           try {
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
                   $middleware = new $this->params['middleware']();
                   if (!$middleware->handle()) {
                       header("Location: {$this->basePath}/login");
                       exit();
                   }
               }

               return $controllerObject->$action();
           } catch (\Exception $e) {
               throw $e;
           }
       }

       $this->handleNotFound();
   }

   private function cleanUrl($url) {
       $parsedUrl = parse_url($url, PHP_URL_PATH);
       
       // Remove base path
       $parsedUrl = substr($parsedUrl, strlen($this->basePath));
       
       // Clean slashes and return
       $parsedUrl = trim($parsedUrl, '/');
       return empty($parsedUrl) ? '/' : '/' . $parsedUrl;
   }

   private function handleNotFound() {
       if ($this->notFoundCallback) {
           call_user_func($this->notFoundCallback);
       } else {
           header("HTTP/1.0 404 Not Found");
           echo '404 Not Found';
       }
   }

   public function url($path) {
       return $this->basePath . '/' . ltrim($path, '/');
   }
}