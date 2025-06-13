<?php
require_once __DIR__ . '/../../database/Database.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Message.php';

/**
 * Dashboard Service
 */
class DashboardService {
    private $db;
    private $messageModel;
    private $userModel;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->messageModel = new Message();
        $this->userModel = new User();
    }
    
    public function getDashboardData(): array {
        // Get message statistics
        $query = "SELECT u.username, COUNT(m.id) as message_count 
                  FROM users u 
                  LEFT JOIN messages m ON u.id = m.user_id 
                  WHERE m.role = 'model' 
                  GROUP BY u.id";
        $messageStats = $this->db->query($query)->fetchAll();
        
        // Get daily access count
        $today = date('Y-m-d');
        $query = "SELECT COUNT(*) as count FROM access_logs WHERE DATE(access_time) = ?";
        $accessCount = $this->db->query($query, [$today])->fetch()['count'] ?? 0;
        
        // Get recent messages
        $query = "SELECT m.content, m.role, u.username, m.created_at 
                  FROM messages m 
                  JOIN users u ON m.user_id = u.id 
                  ORDER BY m.created_at DESC 
                  LIMIT 20";
        $recentMessages = $this->db->query($query)->fetchAll();
        
        return [
            'messageStats' => $messageStats,
            'accessCount' => $accessCount,
            'recentMessages' => $recentMessages
        ];
    }
}
