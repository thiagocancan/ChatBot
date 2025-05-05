<?php
// Process the user message and return the AI response
require_once 'Chatbot.php';
require_once 'config.php';
require_once 'factory/AIClientFactory.php';
require_once 'auth/Auth.php';

// Enable error reporting for debugging
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize authentication
$auth = new Auth();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'error' => 'User not logged in',
        'message' => 'Por favor, faça login para continuar.',
        'redirect' => 'auth/login.php'
    ]);
    exit;
}

// Get configuration
$aiProvider = getAIProvider();
$apiKey = getAPIKey($aiProvider);

// If no API key is found, return an error
if (!$apiKey) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'error' => 'API key not configured',
        'message' => 'Por favor, configure a chave da API antes de usar o chatbot.',
        'setup_required' => true
    ]);
    exit;
}

$aiRole = getAIRole();
$userId = $auth->getUserId();

// Create AI client using the factory
$aiClient = AIClientFactory::createClient($aiProvider, $apiKey);

// Initialize the chatbot with the AI client, role, and user ID
$chatbot = new Chatbot($aiClient, $aiRole, $userId);

// Process the user message
if (isset($_POST['user_message'])) {
    $userMessage = $_POST['user_message'];
    
    try {
        $response = $chatbot->processMessage($userMessage);
        
        // Log the response for debugging
        if (DEBUG_MODE) {
            error_log('Chatbot response: ' . $response);
        }
        
        // Ensure we're sending a valid JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'response' => $response
        ]);
    } catch (Exception $e) {
        // Log the error
        error_log('Chatbot error: ' . $e->getMessage());
        
        // Send error response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage(),
            'message' => 'Ocorreu um erro ao processar sua mensagem. Por favor, tente novamente.'
        ]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'No message provided']);
}
