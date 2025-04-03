<?php
/**
 * Message class to represent a chat message
 */
class Message {
    private $role;
    private $content;
    
    /**
     * Constructor
     * 
     * @param string $role The role of the message sender (user or model)
     * @param string $content The content of the message
     */
    public function __construct(string $role, string $content) {
        $this->role = $role;
        $this->content = $content;
    }
    
    /**
     * Get the role
     * 
     * @return string The role
     */
    public function getRole(): string {
        return $this->role;
    }
    
    /**
     * Get the content
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
            'role' => $this->role,
            'content' => $this->content
        ];
    }
}