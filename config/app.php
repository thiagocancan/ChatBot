<?php
/**
 * Application configuration
 */

// Load environment variables
require_once __DIR__ . '/../core/DotEnv.php';
$dotenv = new DotEnv(__DIR__ . '/../.env');
$dotenv->load();

// Application settings
define('APP_NAME', 'ChatFast');
define('APP_VERSION', '1.0.0');
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true');
define('MAX_HISTORY', getenv('MAX_HISTORY') ? (int)getenv('MAX_HISTORY') : 10);

// Database configuration
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'chatbot_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

// AI Configuration functions
function getGeminiApiKey() {
    $apiKey = getenv('GEMINI_API_KEY');
    if (!$apiKey && isset($_SESSION['GEMINI_API_KEY'])) {
        $apiKey = $_SESSION['GEMINI_API_KEY'];
    }
    return $apiKey;
}

function getAIRole() {
    $role = getenv('AI_ROLE');
    if (!$role && isset($_SESSION['AI_ROLE'])) {
        $role = $_SESSION['AI_ROLE'];
    }
    if (!$role) {
        $roleFile = __DIR__ . '/../ai_role.txt';
        if (file_exists($roleFile)) {
            $role = file_get_contents($roleFile);
        } else {
            $role = 'Você é um assistente de um marketplace.';
        }
    }
    return $role;
}

function getAIProvider() {
    $provider = getenv('AI_PROVIDER');
    if (!$provider && isset($_SESSION['AI_PROVIDER'])) {
        $provider = $_SESSION['AI_PROVIDER'];
    }
    return $provider ?: 'gemini';
}

function getAPIKey(string $provider) {
    switch (strtolower($provider)) {
        case 'gemini':
            return getGeminiApiKey();
        default:
            return '';
    }
}
