<?php
/**
 * Client for interacting with the Google Gemini API
 */
class GeminiClient {
    private $apiKey;
    private $apiEndpoint;
    private $model = 'gemini-1.5-flash'; // Default model
    
    /**
     * Constructor
     * 
     * @param string $apiKey Gemini API key
     */
    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
        $this->apiEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
    }
    
    /**
     * Generate a response from the Gemini API
     * 
     * @param array $conversation The conversation history
     * @return string The AI's response
     */
    public function generateResponse(array $conversation): string {
        // Convert conversation history to Gemini format
        $contents = $this->formatConversation($conversation);
        
        // Prepare the request data
        $data = [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1000,
                'topP' => 0.95,
                'topK' => 40
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];
        
        // Initialize cURL session
        $ch = curl_init($this->apiEndpoint);
        
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
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
            error_log('Gemini API Response: ' . print_r($responseData, true));
        }
        
        // Check for API errors
        if (isset($responseData['error'])) {
            throw new Exception('API error: ' . $responseData['error']['message']);
        }
        
        // Extract and return the AI's message
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            return $responseData['candidates'][0]['content']['parts'][0]['text'];
        } else {
            throw new Exception('Unexpected API response structure: ' . json_encode($responseData));
        }
    }
    
    /**
     * Format the conversation history for Gemini API
     * 
     * @param array $conversation The conversation history
     * @return array Formatted conversation for Gemini
     */
    private function formatConversation(array $conversation): array {
        $contents = [];
        
        foreach ($conversation as $message) {
            $role = $message['role'] === 'user' ? 'user' : 'model';
            
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $message['content']]
                ]
            ];
        }
        
        return $contents;
    }
    
    /**
     * Set the model to use
     * 
     * @param string $model The model name
     */
    public function setModel(string $model): void {
        $this->model = $model;
        // Update the API endpoint with the new model
        $this->apiEndpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";
    }
}