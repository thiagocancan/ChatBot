<?php
require_once __DIR__ . '/../database/Database.php';

/**
 * User model class
 */
class User {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->query("SELECT * FROM `users` WHERE `id` = ?", [$id]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    /**
     * Get user by username
     * 
     * @param string $username Username
     * @return array|null User data or null if not found
     */
    public function getByUsername(string $username): ?array {
        $stmt = $this->db->query("SELECT * FROM `users` WHERE `username` = ?", [$username]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    /**
     * Get user by email
     * 
     * @param string $email Email
     * @return array|null User data or null if not found
     */
    public function getByEmail(string $email): ?array {
        $stmt = $this->db->query("SELECT * FROM `users` WHERE `email` = ?", [$email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    /**
     * Create a new user
     * 
     * @param string $username Username
     * @param string $password Password (will be hashed)
     * @param string $email Email
     * @param string $role User role (admin or user)
     * @return int|false User ID or false on failure
     */
    public function create(string $username, string $password, string $email, string $role = 'user') {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $this->db->query(
                "INSERT INTO `users` (`username`, `password`, `email`, `role`) VALUES (?, ?, ?, ?)",
                [$username, $hashedPassword, $email, $role]
            );
            
            return (int)$this->db->lastInsertId();
        } catch (Exception $e) {
            error_log('User creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user
     * 
     * @param int $id User ID
     * @param array $data User data to update
     * @return bool Success status
     */
    public function update(int $id, array $data): bool {
        // Build update query
        $fields = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            // Handle password separately
            if ($field === 'password') {
                $value = password_hash($value, PASSWORD_DEFAULT);
            }
            
            $fields[] = "`$field` = ?";
            $values[] = $value;
        }
        
        // Add user ID to values
        $values[] = $id;
        
        try {
            $this->db->query(
                "UPDATE `users` SET " . implode(', ', $fields) . " WHERE `id` = ?",
                $values
            );
            
            return true;
        } catch (Exception $e) {
            error_log('User update failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete user
     * 
     * @param int $id User ID
     * @return bool Success status
     */
    public function delete(int $id): bool {
        try {
            $this->db->query("DELETE FROM `users` WHERE `id` = ?", [$id]);
            return true;
        } catch (Exception $e) {
            error_log('User deletion failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify user password
     * 
     * @param string $password Plain text password
     * @param string $hashedPassword Hashed password from database
     * @return bool True if password is correct
     */
    public function verifyPassword(string $password, string $hashedPassword): bool {
        return password_verify($password, $hashedPassword);
    }
    
    /**
     * Get all users
     * 
     * @return array Array of users
     */
    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM `users` ORDER BY `created_at` DESC");
        return $stmt->fetchAll();
    }
}
