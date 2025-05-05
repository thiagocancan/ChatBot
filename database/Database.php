<?php
/**
 * Database connection class
 */
class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Constructor - establishes database connection
     */
    private function __construct() {
        // Get database configuration from environment variables
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_NAME') ?: 'chatbot_db';
        $username = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';
        
        try {
            // Create PDO connection
            $this->connection = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log('Database connection established successfully');
            }
        } catch (PDOException $e) {
            // Log error and throw exception
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('Database connection failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get database instance (Singleton pattern)
     * 
     * @return Database Database instance
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * Get database connection
     * 
     * @return PDO PDO connection
     */
    public function getConnection(): PDO {
        return $this->connection;
    }
    
    /**
     * Execute a query
     * 
     * @param string $query SQL query
     * @param array $params Query parameters
     * @return PDOStatement|false PDO statement
     */
    public function query(string $query, array $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query execution failed: ' . $e->getMessage());
            throw new Exception('Query execution failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get last inserted ID
     * 
     * @return string Last inserted ID
     */
    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }
}
