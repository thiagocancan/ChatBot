<?php
require_once 'GeminiClient.php';
require_once 'Message.php';

/**
 * Chatbot class that handles the conversation logic
 */
class Chatbot {
    private $geminiClient;
    private $conversation = [];
    
    /**
     * Constructor
     * 
     * @param string $apiKey Gemini API key
     */
    public function __construct(string $apiKey) {
        $this->geminiClient = new GeminiClient($apiKey);
        
        // Start a session to store conversation history
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize chat history if not exists
        if (!isset($_SESSION['chat_history'])) {
            $_SESSION['chat_history'] = [];
        }
        
        $this->conversation = $_SESSION['chat_history'];
    }
    
    /**
     * Process a user message and get a response from the AI
     * 
     * @param string $userMessage The message from the user
     * @return string The AI's response
     */
    public function processMessage(string $userMessage): string {
        // Create a new user message
        $message = new Message('user', $userMessage);
        
        // Add to conversation history
        $this->addMessageToConversation($message);
        
        // Get response from Gemini
        $aiResponse = $this->geminiClient->generateResponse($this->conversation);
        
        // Create a new AI message
        $responseMessage = new Message('model', $aiResponse);
        
        // Add to conversation history
        $this->addMessageToConversation($responseMessage);
        
        return $aiResponse;
    }
    
    /**
     * Add a message to the conversation history
     * 
     * @param Message $message The message to add
     */
    private function addMessageToConversation(Message $message): void {
        $this->conversation[] = $message->toArray();
        $_SESSION['chat_history'] = $this->conversation;
    }
    
    /**
     * Clear the conversation history
     */
    public function clearConversation(): void {
        $this->conversation = [];
        $_SESSION['chat_history'] = [];
    }
    
    /**
     * Get the current conversation history
     * 
     * @return array The conversation history
     */
    public function getConversation(): array {
        return $this->conversation;
    }
}