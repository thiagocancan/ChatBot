<?php
require_once __DIR__ . '/../database/Database.php';

/**
 * Message model class
 */
class Message {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get message by ID
     * 
     * @param int $id Message ID
     * @return array|null Message data or null if not found
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->query("SELECT * FROM `messages` WHERE `id` = ?", [$id]);
        $message = $stmt->fetch();
        
        return $message ?: null;
    }
    
    /**
     * Create a new message
     * 
     * @param int $userId User ID
     * @param string $role Message role (user, model, system)
     * @param string $content Message content
     * @return int|false Message ID or false on failure
     */
    public function create(int $userId, string $role, string $content) {
        try {
            $this->db->query(
                "INSERT INTO `messages` (`user_id`, `role`, `content`) VALUES (?, ?, ?)",
                [$userId, $role, $content]
            );
            
            return (int)$this->db->lastInsertId();
        } catch (Exception $e) {
            error_log('Message creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get messages by user ID
     * 
     * @param int $userId User ID
     * @param int $limit Maximum number of messages to retrieve (0 for all)
     * @return array Array of messages
     */
    public function getByUser(int $userId, int $limit = 0): array {
        $query = "SELECT * FROM `messages` WHERE `user_id` = ? ORDER BY `created_at` ASC";
        
        if ($limit > 0) {
            $query .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->query($query, [$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Delete message
     * 
     * @param int $id Message ID
     * @return bool Success status
     */
    public function delete(int $id): bool {
        try {
            $this->db->query("DELETE FROM `messages` WHERE `id` = ?", [$id]);
            return true;
        } catch (Exception $e) {
            error_log('Message deletion failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete all messages for a user
     * 
     * @param int $userId User ID
     * @return bool Success status
     */
    public function deleteByUser(int $userId): bool {
        try {
            $this->db->query("DELETE FROM `messages` WHERE `user_id` = ?", [$userId]);
            return true;
        } catch (Exception $e) {
            error_log('Message deletion failed: ' . $e->getMessage());
            return false;
        }
    }
}
