<?php
/**
 * Abstract base class for all message types
 */
abstract class AbstractMessage {
    protected $content;
    
    /**
     * Constructor
     * 
     * @param string $content The content of the message
     */
    public function __construct(string $content) {
        $this->content = $content;
    }
    
    /**
     * Get the role of the message
     * 
     * @return string The role
     */
    abstract public function getRole(): string;
    
    /**
     * Get the content of the message
     * 
     * @return string The content
     */
    public function getContent(): string {
        return $this->content;
    }
    
    /**
     * Convert the message to an array
     * 
     * @return array The message as an array
     */
    public function toArray(): array {
        return [
            'role' => $this->getRole(),
            'content' => $this->content
        ];
    }
}
