<?php
require_once __DIR__ . '/../interfaces/AIClientInterface.php';
require_once __DIR__ . '/../clients/GeminiClient.php';

/**
 * Factory class for creating AI clients
 */
class AIClientFactory {
    /**
     * Create an AI client based on the provider
     * 
     * @param string $provider The AI provider (gemini, openai)
     * @param string $apiKey The API key for the provider
     * @return AIClientInterface The AI client
     */
    public static function createClient(string $provider, string $apiKey): AIClientInterface {
        switch (strtolower($provider)) {
            case 'gemini':
                return new GeminiClient($apiKey);
            default:
                throw new Exception("Unsupported AI provider: {$provider}");
        }
    }
}
