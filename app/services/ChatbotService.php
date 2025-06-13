<?php
require_once __DIR__ . '/../../Chatbot.php';
require_once __DIR__ . '/../../factory/AIClientFactory.php';
require_once __DIR__ . '/../../config/app.php';

/**
 * Chatbot Service
 */
class ChatbotService {
    
    public function initializeChatbot(?int $userId): Chatbot {
        $aiProvider = getAIProvider();
        $apiKey = getAPIKey($aiProvider);
        $aiRole = getAIRole();
        
        if (!$apiKey) {
            throw new Exception('API key not configured');
        }
        
        // Create AI client using the factory
        $aiClient = AIClientFactory::createClient($aiProvider, $apiKey);
        
        // Create chatbot with the AI client, role, and user ID
        return new Chatbot($aiClient, $aiRole, $userId);
    }
    
    public function processMessage(int $userId, string $userMessage): string {
        $chatbot = $this->initializeChatbot($userId);
        return $chatbot->processMessage($userMessage);
    }
}
