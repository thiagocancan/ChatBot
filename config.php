<?php
/**
 * Configuration file for the chatbot
 * 
 * This file loads environment variables from .env file
 * and provides configuration for the application
 */

// Load environment variables from .env file
require_once __DIR__ . '/DotEnv.php';
$dotenv = new DotEnv(__DIR__ . '/.env');
$dotenv->load();

// Gemini API configuration
function getGeminiApiKey() {
    // First check for environment variable (for production/hosting environments)
    $apiKey = getenv('GEMINI_API_KEY');
    
    // If not found and we're in development, check for a session variable
    if (!$apiKey && isset($_SESSION['GEMINI_API_KEY'])) {
        $apiKey = $_SESSION['GEMINI_API_KEY'];
    }
    
    return $apiKey;
}

// Application configuration
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true');
define('MAX_HISTORY', getenv('MAX_HISTORY') ? (int)getenv('MAX_HISTORY') : 10);