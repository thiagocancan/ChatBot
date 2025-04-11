<?php
require_once __DIR__ . '/AbstractMessage.php';

/**
 * Class for system messages
 */
class SystemMessage extends AbstractMessage {
    /**
     * Get the role of the message
     * 
     * @return string The role
     */
    public function getRole(): string {
        return 'system';
    }
}
