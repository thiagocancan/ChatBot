<?php
/**
 * Installation script
 * 
 * This script sets up the application by:
 * 1. Creating a .env file if it doesn't exist
 * 2. Setting up the database
 */

// Check if .env file exists
if (!file_exists(__DIR__ . '/.env')) {
    // Copy .env.example to .env
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', __DIR__ . '/.env');
        echo "Created .env file from .env.example\n";
        echo "Please edit the .env file with your configuration settings.\n";
    } else {
        die(".env.example file not found. Cannot create .env file.\n");
    }
}

// Load environment variables
require_once __DIR__ . '/DotEnv.php';
$dotenv = new DotEnv(__DIR__ . '/.env');
$dotenv->load();

// Check if database credentials are set
$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');
$dbName = getenv('DB_NAME');

if (!$dbHost || !$dbUser || !$dbName) {
    echo "Database configuration is incomplete in .env file.\n";
    echo "Please update your .env file with the database credentials.\n";
    exit(1);
}

// Set up the database
echo "Setting up the database...\n";
require_once __DIR__ . '/database/setup.php';

echo "\nInstallation completed!\n";
echo "You can now access the application.\n";
