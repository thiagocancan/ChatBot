<?php
/**
 * Interface for AI clients
 */
interface AIClientInterface {
    /**
     * Generate a response from the AI
     * 
     * @param array $conversation The conversation history
     * @return string The AI's response
     */
    public function generateResponse(array $conversation): string;
    
    /**
     * Set the model to use
     * 
     * @param string $model The model name
     */
    public function setModel(string $model): void;
}
