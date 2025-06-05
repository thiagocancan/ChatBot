<?php
/**
 * Database setup script
 * 
 * This script creates the database and tables if they don't exist
 */
require_once __DIR__ . '/../DotEnv.php';
$dotenv = new DotEnv(__DIR__ . '/../.env');
$dotenv->load();

// Database configuration
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'chatbot_db';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    // Connect to MySQL server (without database)
    $pdo = new PDO(
        "mysql:host=$host;port=$port;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$dbname' created or already exists.\n";
    
    // Connect to the database
    $pdo->exec("USE `$dbname`");
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `email` VARCHAR(100) NOT NULL UNIQUE,
            `role` ENUM('admin', 'user') NOT NULL DEFAULT 'user',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Table 'users' created or already exists.\n";
    
    // Create messages table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `messages` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `role` ENUM('user', 'model', 'system') NOT NULL,
            `content` TEXT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Table 'messages' created or already exists.\n";
    
    // Create access_logs table for tracking user logins
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `access_logs` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT UNSIGNED NOT NULL,
            `access_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `ip_address` VARCHAR(45) NULL,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Table 'access_logs' created or already exists.\n";
    
    // Check if admin user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE `role` = 'admin'");
    $stmt->execute();
    $adminExists = (int)$stmt->fetchColumn() > 0;
    
    // Create default admin user if none exists
    if (!$adminExists) {
        // Generate a random password
        $defaultPassword = bin2hex(random_bytes(8)); // 16 characters
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO `users` (`username`, `password`, `email`, `role`)
            VALUES ('admin', :password, 'admin@example.com', 'admin')
        ");
        $stmt->execute(['password' => $hashedPassword]);
        
        echo "Default admin user created with username 'admin' and password '$defaultPassword'\n";
        echo "IMPORTANT: Please change this password immediately after logging in!\n";
    } else {
        echo "Admin user already exists.\n";
    }
    
    echo "Database setup completed successfully!\n";
    
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage() . "\n");
}
