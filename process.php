<?php
// Process the user message and return the AI response
require_once 'Chatbot.php';
require_once 'config.php';

// Enable error reporting for debugging
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if we have an API key
$apiKey = getGeminiApiKey();

// If no API key is found, return an error
if (!$apiKey) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'error' => 'API key not configured',
        'message' => 'Por favor, configure a chave da API Gemini antes de usar o chatbot.',
        'setup_required' => true
    ]);
    exit;
}

// Get the AI role
$aiRole = getAIRole();

// Initialize the chatbot with the Gemini API key and role
$chatbot = new Chatbot($apiKey, $aiRole);

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
        echo json_encode(['success' => true, 'response' => $response]);
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