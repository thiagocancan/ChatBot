<?php
/**
 * Router class - Clean MVC implementation
 */
class Router {
    private $routes = [];
    private $currentRoute = null;
    private $basePath = '';
    
    public function __construct() {
        // Detect base path for XAMPP
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $this->basePath = dirname($scriptName);
        if ($this->basePath === '\\' || $this->basePath === '/') {
            $this->basePath = '';
        }
    }
    
    public function get($path, $controller, $method = 'index') {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }
    
    public function post($path, $controller, $method = 'store') {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }
    
    public function dispatch($route = null) {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Use provided route or get from URL
        if ($route === null) {
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            // Remove base path
            if ($this->basePath && strpos($requestUri, $this->basePath) === 0) {
                $requestUri = substr($requestUri, strlen($this->basePath));
            }
            
            // Normalize path
            if ($requestUri === '' || $requestUri === '/') {
                $requestUri = '/';
            }
            
            $route = $requestUri;
        }
        
        // Normalize route
        if ($route === '' || $route === '/') {
            $route = '/';
        }
        
        // Add leading slash if missing
        if (strpos($route, '/') !== 0) {
            $route = '/' . $route;
        }
        
        // Debug info for development
        if (DEBUG_MODE) {
            error_log("Router Debug - Method: $requestMethod, Route: $route, Base: {$this->basePath}");
        }
        
        // Check if route exists
        if (isset($this->routes[$requestMethod][$route])) {
            $routeData = $this->routes[$requestMethod][$route];
            $this->currentRoute = $routeData;
            
            $controllerName = $routeData['controller'];
            $methodName = $routeData['method'];
            
            // Load controller
            $controllerFile = __DIR__ . '/../app/controllers/' . $controllerName . '.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        return $controller->$methodName();
                    } else {
                        throw new Exception("Method $methodName not found in $controllerName");
                    }
                } else {
                    throw new Exception("Controller class $controllerName not found");
                }
            } else {
                throw new Exception("Controller file not found: $controllerFile");
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        $this->show404($route);
    }
    
    private function show404($route) {
        echo "<!DOCTYPE html>";
        echo "<html><head><title>404 - Página não encontrada</title>";
        echo "<style>body{font-family:Arial,sans-serif;margin:50px;} .container{max-width:600px;margin:0 auto;text-align:center;}</style>";
        echo "</head><body>";
        echo "<div class='container'>";
        echo "<h1>404 - Página não encontrada</h1>";
        echo "<p>A página que você está procurando não existe.</p>";
        echo "<p><strong>Rota solicitada:</strong> $route</p>";
        echo "<a href='{$this->basePath}/'>Voltar ao início</a>";
        echo "</div></body></html>";
    }
    
    public function getCurrentRoute() {
        return $this->currentRoute;
    }
    
    public function getBasePath() {
        return $this->basePath;
    }
}
