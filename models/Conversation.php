<?php
require_once __DIR__ . '/../database/Database.php';

/**
 * Conversation model class
 */
class Conversation {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get conversation by ID
     * 
     * @param int $id Conversation ID
     * @return array|null Conversation data or null if not found
     */
    public function getById(int $id): ?array {
        $stmt = $this->db->query("SELECT * FROM `conversations` WHERE `id` = ?", [$id]);
        $conversation = $stmt->fetch();
        
        return $conversation ?: null;
    }
    
    /**
     * Create a new conversation
     * 
     * @param int|null $userId User ID (null for guest conversations)
     * @param string $title Conversation title
     * @return int|false Conversation ID or false on failure
     */
    public function create(?int $userId, string $title = 'New Conversation') {
        try {
            $this->db->query(
                "INSERT INTO `conversations` (`user_id`, `title`) VALUES (?, ?)",
                [$userId, $title]
            );
            
            return (int)$this->db->lastInsertId();
        } catch (Exception $e) {
            error_log('Conversation creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update conversation
     * 
     * @param int $id Conversation ID
     * @param array $data Conversation data to update
     * @return bool Success status
     */
    public function update(int $id, array $data): bool {
        // Build update query
        $fields = [];
        $values = [];
        
        foreach ($data as $field => $value) {
            $fields[] = "`$field` = ?";
            $values[] = $value;
        }
        
        // Add conversation ID to values
        $values[] = $id;
        
        try {
            $this->db->query(
                "UPDATE `conversations` SET " . implode(', ', $fields) . " WHERE `id` = ?",
                $values
            );
            
            return true;
        } catch (Exception $e) {
            error_log('Conversation update failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete conversation
     * 
     * @param int $id Conversation ID
     * @return bool Success status
     */
    public function delete(int $id): bool {
        try {
            $this->db->query("DELETE FROM `conversations` WHERE `id` = ?", [$id]);
            return true;
        } catch (Exception $e) {
            error_log('Conversation deletion failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get conversations by user ID
     * 
     * @param int $userId User ID
     * @return array Array of conversations
     */
    public function getByUser(int $userId): array {
        $stmt = $this->db->query(
            "SELECT * FROM `conversations` WHERE `user_id` = ? ORDER BY `updated_at` DESC",
            [$userId]
        );
        
        return $stmt->fetchAll();
    }
}
