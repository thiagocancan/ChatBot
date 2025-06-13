<?php
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../database/Database.php';

/**
 * Authentication Service
 */
class AuthService {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function register(string $username, string $password, string $email, string $role = 'user') {
        if ($this->userModel->getByUsername($username)) {
            return false;
        }
        
        if ($this->userModel->getByEmail($email)) {
            return false;
        }
        
        return $this->userModel->create($username, $password, $email, $role);
    }
    
    public function login(string $username, string $password): bool {
        $user = $this->userModel->getByUsername($username);
        
        if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            $this->logAccess($user['id']);
            
            return true;
        }
        
        return false;
    }
    
    private function logAccess(int $userId): void {
        try {
            $db = Database::getInstance();
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
            
            $db->query(
                "INSERT INTO `access_logs` (`user_id`, `ip_address`) VALUES (?, ?)",
                [$userId, $ipAddress]
            );
        } catch (Exception $e) {
            error_log('Failed to log access: ' . $e->getMessage());
        }
    }
    
    public function logout(): void {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['role']);
        session_destroy();
    }
    
    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin(): bool {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function getUsername(): ?string {
        return $_SESSION['username'] ?? null;
    }
    
    public function getRole(): ?string {
        return $_SESSION['role'] ?? null;
    }
    
    public function getUser(): ?array {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->userModel->getById($_SESSION['user_id']);
    }
}
