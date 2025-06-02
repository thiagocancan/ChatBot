<?php
require_once __DIR__ . '/interfaces/AIClientInterface.php';
require_once __DIR__ . '/messages/UserMessage.php';
require_once __DIR__ . '/messages/AIMessage.php';
require_once __DIR__ . '/messages/SystemMessage.php';
require_once __DIR__ . '/models/Message.php';

/**
 * Classe Chatbot que gerencia a lógica da conversa
 */
class Chatbot {
    private $aiClient;
    private $conversation = [];
    private $aiRole;
    private $userId;
    private $messageModel;
    
    /**
     * Construtor
     * 
     * @param AIClientInterface $aiClient O cliente de IA a ser usado
     * @param string $aiRole O papel / prompt do sistema para a IA
     * @param int|null $userId O ID do usuário (null para usuários convidados)
     */
    public function __construct(AIClientInterface $aiClient, string $aiRole = '', ?int $userId = null) {
        $this->aiClient = $aiClient;
        $this->aiRole = $aiRole;
        $this->userId = $userId;
        $this->messageModel = new Message();
        
        // Iniciar uma sessão para armazenar o histórico de conversas de convidados
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Inicializar a conversa
        $this->initializeConversation();
    }
    
    /**
     * Inicializar a conversa
     */
    private function initializeConversation(): void {
        // Se o usuário estiver logado, carregar mensagens do banco de dados
        if ($this->userId) {
            $this->loadMessagesFromDatabase();
            
            // Verificar se precisamos adicionar a mensagem do sistema
            if (!empty($this->aiRole) && empty($this->conversation)) {
                $this->initializeWithRole();
            }
        } else {
            // Para usuários convidados, usar conversa baseada em sessão
            if (!isset($_SESSION['chat_history'])) {
                $_SESSION['chat_history'] = [];
                
                // Se o papel da IA estiver definido, adicioná-lo como a primeira mensagem do sistema
                if (!empty($this->aiRole)) {
                    $roleMessage = new SystemMessage($this->aiRole);
                    $_SESSION['chat_history'][] = $roleMessage->toArray();
                }
            }
            
            $this->conversation = $_SESSION['chat_history'];
        }
    }
    
    /**
     * Carregar mensagens do banco de dados
     */
    private function loadMessagesFromDatabase(): void {
        // Obter todas as mensagens para este usuário
        $messages = $this->messageModel->getByUser($this->userId);
        
        // Converter para o formato esperado pelo cliente de IA
        $this->conversation = [];
        foreach ($messages as $message) {
            $this->conversation[] = [
                'role' => $message['role'],
                'content' => $message['content']
            ];
        }
    }
    
    /**
     * Inicializar a conversa com o papel da IA
     */
    private function initializeWithRole(): void {
        // Criar uma mensagem do sistema com o papel da IA
        $roleMessage = new SystemMessage($this->aiRole);
        
        // Adicionar ao histórico da conversa
        if ($this->userId) {
            // Armazenar no banco de dados
            $this->messageModel->create(
                $this->userId,
                $roleMessage->getRole(),
                $roleMessage->getContent()
            );
        }
        
        // Adicionar à conversa em memória
        $this->conversation[] = $roleMessage->toArray();
    }
    
    /**
     * Processar uma mensagem do usuário e obter uma resposta da IA
     * 
     * @param string $userMessage A mensagem do usuário
     * @return string A resposta da IA
     */
    public function processMessage(string $userMessage): string {
        // Criar uma nova mensagem do usuário
        $message = new UserMessage($userMessage);
        
        // Adicionar ao histórico da conversa
        $this->addMessageToConversation($message);
        
        // Obter resposta da IA
        $aiResponse = $this->aiClient->generateResponse($this->conversation);
        
        // Criar uma nova mensagem da IA
        $responseMessage = new AIMessage($aiResponse);
        
        // Adicionar ao histórico da conversa
        $this->addMessageToConversation($responseMessage);
        
        return $aiResponse;
    }
    
    /**
     * Adicionar uma mensagem ao histórico da conversa
     * 
     * @param AbstractMessage $message A mensagem a ser adicionada
     */
    private function addMessageToConversation(AbstractMessage $message): void {
        // Adicionar à conversa em memória
        $this->conversation[] = $message->toArray();
        
        // Armazenar no banco de dados se o usuário estiver logado
        if ($this->userId) {
            $this->messageModel->create(
                $this->userId,
                $message->getRole(),
                $message->getContent()
            );
        } else {
            // Armazenar na sessão para usuários convidados
            $_SESSION['chat_history'] = $this->conversation;
        }
    }
    
    /**
     * Limpar o histórico da conversa
     */
    public function clearConversation(): void {
        $this->conversation = [];
        
        // Limpar a conversa no banco de dados se aplicável
        if ($this->userId) {
            $this->messageModel->deleteByUser($this->userId);
            
            // Re-inicializar com o papel, se definido
            if (!empty($this->aiRole)) {
                $this->initializeWithRole();
            }
        } else {
            // Limpar a sessão para usuários convidados
            $_SESSION['chat_history'] = [];
            
            // Re-inicializar com o papel, se definido
            if (!empty($this->aiRole)) {
                $roleMessage = new SystemMessage($this->aiRole);
                $_SESSION['chat_history'][] = $roleMessage->toArray();
                $this->conversation = $_SESSION['chat_history'];
            }
        }
    }
    
    /**
     * Obter o histórico atual da conversa
     * 
     * @return array O histórico da conversa
     */
    public function getConversation(): array {
        return $this->conversation;
    }
    
    /**
     * Definir o cliente de IA
     * 
     * @param AIClientInterface $aiClient O cliente de IA a ser usado
     */
    public function setAIClient(AIClientInterface $aiClient): void {
        $this->aiClient = $aiClient;
    }
    
    /**
     * Definir o ID do usuário
     * 
     * @param int $userId O ID do usuário
     */
    public function setUserId(int $userId): void {
        $this->userId = $userId;
    }
}
