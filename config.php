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

// AI Role configuration
function getAIRole() {
    // First check for environment variable
    $role = getenv('AI_ROLE');
    
    // If not found, check for a session variable
    if (!$role && isset($_SESSION['AI_ROLE'])) {
        $role = $_SESSION['AI_ROLE'];
    }
    
    // If still not found or empty, load from file
    if (!$role) {
        $roleFile = __DIR__ . '/ai_role.txt';
        if (file_exists($roleFile)) {
            $role = file_get_contents($roleFile);
        } else {
            $role = 'Você é um assistente de um marketplace.'; // Default empty role
        }
    }
    
    return $role;
}

// Application configuration
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true');
define('MAX_HISTORY', getenv('MAX_HISTORY') ? (int)getenv('MAX_HISTORY') : 10);