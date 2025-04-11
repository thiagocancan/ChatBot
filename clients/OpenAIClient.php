<?php
require_once 'interfaces/AIClientInterface.php';

/**
 * Client for interacting with the OpenAI API
 */
class OpenAIClient implements AIClientInterface {
    private $apiKey;
    private $apiEndpoint = 'https://api.openai.com/v1/chat/completions';
    private $model = 'gpt-3.5-turbo'; // Default model
    
    /**
     * Constructor
     * 
     * @param string $apiKey OpenAI API key
     */
    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }
    
    /**
     * Generate a response from the OpenAI API
     * 
     * @param array $conversation The conversation history
     * @return string The AI's response
     */
    public function generateResponse(array $conversation): string {
        // Convert conversation history to OpenAI format
        $messages = $this->formatConversation($conversation);
        
        // Prepare the request data
        $data = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1000,
            'top_p' => 0.95
        ];
        
        // Initialize cURL session
        $ch = curl_init($this->apiEndpoint);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->apiKey
        ]);
        
        // Execute cURL request
        $response = curl_exec($ch);
        
        // Check for errors
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }
        
        // Close cURL session
        curl_close($ch);
        
        // Decode the response
        $responseData = json_decode($response, true);
        
        // Add debug logging
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log('OpenAI API Response: ' . print_r($responseData, true));
        }
        
        // Check for API errors
        if (isset($responseData['error'])) {
            throw new Exception('API error: ' . $responseData['error']['message']);
        }
        
        // Extract and return the AI's message
        if (isset($responseData['choices'][0]['message']['content'])) {
            return $responseData['choices'][0]['message']['content'];
        } else {
            throw new Exception('Unexpected API response structure: ' . json_encode($responseData));
        }
    }
    
    /**
     * Format the conversation history for OpenAI API
     * 
     * @param array $conversation The conversation history
     * @return array Formatted conversation for OpenAI
     */
    private function formatConversation(array $conversation): array {
        $messages = [];
        
        foreach ($conversation as $message) {
            $role = $message['role'] === 'user' ? 'user' : 'assistant';
            
            $messages[] = [
                'role' => $role,
                'content' => $message['content']
            ];
        }
        
        return $messages;
    }
    
    /**
     * Set the model to use
     * 
     * @param string $model The model name
     */
    public function setModel(string $model): void {
        $this->model = $model;
    }
}
