<?php
require_once __DIR__ . '/AbstractMessage.php';

/**
 * Class for AI messages
 */
class AIMessage extends AbstractMessage {
    /**
     * Get the role of the message
     * 
     * @return string The role
     */
    public function getRole(): string {
        return 'model';
    }
}
