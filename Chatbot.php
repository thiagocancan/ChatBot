<?php
require_once __DIR__ . '/interfaces/AIClientInterface.php';
require_once __DIR__ . '/messages/UserMessage.php';
require_once __DIR__ . '/messages/AIMessage.php';
require_once __DIR__ . '/messages/SystemMessage.php';
require_once __DIR__ . '/models/Message.php';

/**
 * Chatbot class that handles the conversation logic
 */
class Chatbot {
    private $aiClient;
    private $conversation = [];
    private $aiRole;
    private $userId;
    private $messageModel;
    
    /**
     * Constructor
     * 
     * @param AIClientInterface $aiClient The AI client to use
     * @param string $aiRole The role/system prompt for the AI
     * @param int|null $userId The user ID (null for guest users)
     */
    public function __construct(AIClientInterface $aiClient, string $aiRole = '', ?int $userId = null) {
        $this->aiClient = $aiClient;
        $this->aiRole = $aiRole;
        $this->userId = $userId;
        $this->messageModel = new Message();
        
        // Start a session to store conversation history for guests
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Initialize conversation
        $this->initializeConversation();
    }
    
    /**
     * Initialize the conversation
     */
    private function initializeConversation(): void {
        // If user is logged in, load messages from database
        if ($this->userId) {
            $this->loadMessagesFromDatabase();
            
            // Check if we need to add the system message
            if (!empty($this->aiRole) && empty($this->conversation)) {
                $this->initializeWithRole();
            }
        } else {
            // For guest users, use session-based conversation
            if (!isset($_SESSION['chat_history'])) {
                $_SESSION['chat_history'] = [];
                
                // If AI role is defined, add it as the first system message
                if (!empty($this->aiRole)) {
                    $roleMessage = new SystemMessage($this->aiRole);
                    $_SESSION['chat_history'][] = $roleMessage->toArray();
                }
            }
            
            $this->conversation = $_SESSION['chat_history'];
        }
    }
    
    /**
     * Load messages from database
     */
    private function loadMessagesFromDatabase(): void {
        // Get all messages for this user
        $messages = $this->messageModel->getByUser($this->userId);
        
        // Convert to the format expected by the AI client
        $this->conversation = [];
        foreach ($messages as $message) {
            $this->conversation[] = [
                'role' => $message['role'],
                'content' => $message['content']
            ];
        }
    }
    
    /**
     * Initialize the conversation with the AI role
     */
    private function initializeWithRole(): void {
        // Create a system message with the AI role
        $roleMessage = new SystemMessage($this->aiRole);
        
        // Add to conversation history
        if ($this->userId) {
            // Store in database
            $this->messageModel->create(
                $this->userId,
                $roleMessage->getRole(),
                $roleMessage->getContent()
            );
        }
        
        // Add to in-memory conversation
        $this->conversation[] = $roleMessage->toArray();
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
        // Add to in-memory conversation
        $this->conversation[] = $message->toArray();
        
        // Store in database if user is logged in
        if ($this->userId) {
            $this->messageModel->create(
                $this->userId,
                $message->getRole(),
                $message->getContent()
            );
        } else {
            // Store in session for guest users
            $_SESSION['chat_history'] = $this->conversation;
        }
    }
    
    /**
     * Clear the conversation history
     */
    public function clearConversation(): void {
        $this->conversation = [];
        
        // Clear database conversation if applicable
        if ($this->userId) {
            $this->messageModel->deleteByUser($this->userId);
            
            // Re-initialize with role if defined
            if (!empty($this->aiRole)) {
                $this->initializeWithRole();
            }
        } else {
            // Clear session for guest users
            $_SESSION['chat_history'] = [];
            
            // Re-initialize with role if defined
            if (!empty($this->aiRole)) {
                $roleMessage = new SystemMessage($this->aiRole);
                $_SESSION['chat_history'][] = $roleMessage->toArray();
                $this->conversation = $_SESSION['chat_history'];
            }
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
    
    /**
     * Set the user ID
     * 
     * @param int $userId The user ID
     */
    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }
}
