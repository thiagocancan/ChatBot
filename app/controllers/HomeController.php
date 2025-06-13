<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../services/ChatbotService.php';

/**
 * Home Controller - Main chat interface
 */
class HomeController extends Controller {
    private $chatbotService;
    
    public function __construct() {
        parent::__construct();
        $this->chatbotService = new ChatbotService();
    }
    
    public function index() {
        // Require authentication
        $this->requireAuth();
        
        // Check if API is configured
        $aiProvider = getAIProvider();
        $apiKey = getAPIKey($aiProvider);
        
        if (!$apiKey) {
            $this->redirect('/admin/setup');
        }
        
        // Get user data
        $userId = $this->auth->getUserId();
        $username = $this->auth->getUsername();
        $isAdmin = $this->auth->isAdmin();
        
        // Initialize chatbot
        $chatbot = $this->chatbotService->initializeChatbot($userId);
        
        // Handle conversation clearing
        if (isset($_GET['clear']) && $_GET['clear'] === 'true') {
            $chatbot->clearConversation();
            $this->redirect('/');
        }
        
        // Get conversation history
        $conversation = $chatbot->getConversation();
        
        // Load view
        $this->view('home/chat', [
            'username' => $username,
            'isAdmin' => $isAdmin,
            'conversation' => $conversation
        ]);
    }
    
    public function processMessage() {
        // Require authentication
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'error' => 'Method not allowed'], 405);
        }
        
        if (!isset($_POST['user_message'])) {
            $this->json(['success' => false, 'error' => 'No message provided']);
        }
        
        try {
            $userId = $this->auth->getUserId();
            $userMessage = $_POST['user_message'];
            
            // Check if API is configured
            $aiProvider = getAIProvider();
            $apiKey = getAPIKey($aiProvider);
            
            if (!$apiKey) {
                $this->json([
                    'success' => false, 
                    'setup_required' => true,
                    'message' => 'API não configurada'
                ]);
            }
            
            // Process message through service
            $response = $this->chatbotService->processMessage($userId, $userMessage);
            
            $this->json(['success' => true, 'response' => $response]);
            
        } catch (Exception $e) {
            error_log('Chatbot error: ' . $e->getMessage());
            
            $this->json([
                'success' => false, 
                'error' => $e->getMessage(),
                'message' => 'Ocorreu um erro ao processar sua mensagem.'
            ]);
        }
    }
}
