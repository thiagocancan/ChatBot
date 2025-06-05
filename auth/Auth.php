<?php
require_once __DIR__ . '/../models/User.php';

/**
 * Authentication class
 */
class Auth {
    private $userModel;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->userModel = new User();
        
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Register a new user
     * 
     * @param string $username Username
     * @param string $password Password
     * @param string $email Email
     * @param string $role User role (admin or user)
     * @return int|false User ID or false on failure
     */
    public function register(string $username, string $password, string $email, string $role = 'user') {
        // Check if username already exists
        if ($this->userModel->getByUsername($username)) {
            return false;
        }
        
        // Check if email already exists
        if ($this->userModel->getByEmail($email)) {
            return false;
        }
        
        // Create user
        return $this->userModel->create($username, $password, $email, $role);
    }
    
    /**
     * Login user
     * 
     * @param string $username Username
     * @param string $password Password
     * @return bool Success status
     */
    public function login(string $username, string $password): bool {
        // Get user by username
        $user = $this->userModel->getByUsername($username);
        
        // Check if user exists and password is correct
        if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Log this access
            $this->logAccess($user['id']);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Log user access
     * 
     * @param int $userId User ID
     */
    private function logAccess(int $userId): void {
        try {
            $db = Database::getInstance();
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            
            $db->query(
                "INSERT INTO `access_logs` (`user_id`, `ip_address`) VALUES (?, ?)",
                [$userId, $ipAddress]
            );
        } catch (Exception $e) {
            // Just log the error but don't interrupt the login process
            error_log('Failed to log access: ' . $e->getMessage());
        }
    }
    
    /**
     * Logout user
     */
    public function logout(): void {
        // Unset user session variables
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['role']);
        
        // Destroy session
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     * 
     * @return bool True if user is logged in
     */
    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user is admin
     * 
     * @return bool True if user is admin
     */
    public function isAdmin(): bool {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    /**
     * Get current user ID
     * 
     * @return int|null User ID or null if not logged in
     */
    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Get current username
     * 
     * @return string|null Username or null if not logged in
     */
    public function getUsername(): ?string {
        return $_SESSION['username'] ?? null;
    }
    
    /**
     * Get current user role
     * 
     * @return string|null User role or null if not logged in
     */
    public function getRole(): ?string {
        return $_SESSION['role'] ?? null;
    }
    
    /**
     * Get current user data
     * 
     * @return array|null User data or null if not logged in
     */
    public function getUser(): ?array {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->userModel->getById($_SESSION['user_id']);
    }
}
