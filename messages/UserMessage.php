<?php
require_once __DIR__ . '/AbstractMessage.php';

/**
 * Class for user messages
 */
class UserMessage extends AbstractMessage {
    /**
     * Get the role of the message
     * 
     * @return string The role
     */
    public function getRole(): string {
        return 'user';
    }
}
