<?php
/**
 * Base Controller class
 */
class Controller {
    protected $auth;
    protected $basePath = '';
    
    public function __construct() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Set base path for URLs
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $this->basePath = dirname($scriptName);
        if ($this->basePath === '\\' || $this->basePath === '/') {
            $this->basePath = '';
        }
        
        // Initialize authentication
        require_once __DIR__ . '/../app/services/AuthService.php';
        $this->auth = new AuthService();
    }
    
    /**
     * Load a view
     */
    protected function view($viewName, $data = []) {
        // Add base path to data
        $data['basePath'] = $this->basePath;
        
        // Extract data to variables
        extract($data);
        
        // Load view file
        $viewFile = __DIR__ . '/../app/views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new Exception("View not found: $viewName");
        }
    }
    
    /**
     * Redirect to a URL
     */
    protected function redirect($url) {
        // Add base path if URL is relative
        if (strpos($url, '/') === 0 && strpos($url, $this->basePath) !== 0) {
            $url = $this->basePath . $url;
        }
        
        header("Location: $url");
        exit;
    }
    
    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Check if user is authenticated
     */
    protected function requireAuth() {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/auth/login');
        }
    }
    
    /**
     * Check if user is admin
     */
    protected function requireAdmin() {
        $this->requireAuth();
        if (!$this->auth->isAdmin()) {
            $this->redirect('/');
        }
    }
}
