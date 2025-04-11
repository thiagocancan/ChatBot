<?php
require_once __DIR__ . '/interfaces/AIClientInterface.php';
require_once __DIR__ . '/messages/UserMessage.php';
require_once __DIR__ . '/messages/AIMessage.php';
require_once __DIR__ . '/messages/SystemMessage.php';

/**
 * Chatbot class that handles the conversation logic
 */
class Chatbot {
    private $aiClient;
    private $conversation = [];
    private $aiRole;
    
    /**
     * Constructor
     * 
     * @param AIClientInterface $aiClient The AI client to use
     * @param string $aiRole The role/system prompt for the AI
     */
    public function __construct(AIClientInterface $aiClient, string $aiRole = '') {
        $this->aiClient = $aiClient;
        $this->aiRole = $aiRole;
        
        // Start a session to store conversation history
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize chat history if not exists
        if (!isset($_SESSION['chat_history'])) {
            $_SESSION['chat_history'] = [];
            
            // If AI role is defined, add it as the first system message
            if (!empty($this->aiRole)) {
                $this->initializeWithRole();
            }
        }
        
        $this->conversation = $_SESSION['chat_history'];
    }
    
    /**
     * Initialize the conversation with the AI role
     */
    private function initializeWithRole(): void {
        // Create a system message with the AI role
        $roleMessage = new SystemMessage($this->aiRole);
        
        // Add to conversation history
        $this->addMessageToConversation($roleMessage);
    }
    
    /**
     * Process a user message and get a response from the AI
     * 
     * @param string $userMessage The message from the user
     * @return string The AI's response
     */
    public function processMessage(string $userMessage): string {
        // Create a new user message
        $message = new UserMessage($userMessage);
        
        // Add to conversation history
        $this->addMessageToConversation($message);
        
        // Get response from AI
        $aiResponse = $this->aiClient->generateResponse($this->conversation);
        
        // Create a new AI message
        $responseMessage = new AIMessage($aiResponse);
        
        // Add to conversation history
        $this->addMessageToConversation($responseMessage);
        
        return $aiResponse;
    }
    
    /**
     * Add a message to the conversation history
     * 
     * @param AbstractMessage $message The message to add
     */
    private function addMessageToConversation(AbstractMessage $message): void {
        $this->conversation[] = $message->toArray();
        $_SESSION['chat_history'] = $this->conversation;
    }
    
    /**
     * Clear the conversation history
     */
    public function clearConversation(): void {
        $this->conversation = [];
        $_SESSION['chat_history'] = [];
        
        // Re-initialize with role if defined
        if (!empty($this->aiRole)) {
            $this->initializeWithRole();
        }
    }
    
    /**
     * Get the current conversation history
     * 
     * @return array The conversation history
     */
    public function getConversation(): array {
        return $this->conversation;
    }
    
    /**
     * Set the AI client
     * 
     * @param AIClientInterface $aiClient The AI client to use
     */
    public function setAIClient(AIClientInterface $aiClient): void {
        $this->aiClient = $aiClient;
    }
}
