<?php
// Processa a mensagem do usuário e retorna a resposta da IA
require_once 'Chatbot.php';
require_once 'config.php';
require_once 'factory/AIClientFactory.php';
require_once 'auth/Auth.php';

// Ativa a exibição de erros para depuração
if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Inicia a sessão, se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializa a autenticação
$auth = new Auth();

// Verifica se o usuário está logado
if (!$auth->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'error' => 'Usuário não está logado',
        'message' => 'Por favor, faça login para continuar.',
        'redirect' => 'auth/login.php'
    ]);
    exit;
}

// Obtém a configuração
$aiProvider = getAIProvider();
$apiKey = getAPIKey($aiProvider);

// Se a chave da API não estiver configurada, retorna erro
if (!$apiKey) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'error' => 'Chave da API não configurada',
        'message' => 'Por favor, configure a chave da API antes de usar o chatbot.',
        'setup_required' => true
    ]);
    exit;
}

$aiRole = getAIRole();
$userId = $auth->getUserId();

// Cria o cliente de IA usando a fábrica
$aiClient = AIClientFactory::createClient($aiProvider, $apiKey);

// Inicializa o chatbot com o cliente de IA, a função e o ID do usuário
$chatbot = new Chatbot($aiClient, $aiRole, $userId);

// Processa a mensagem do usuário
if (isset($_POST['user_message'])) {
    $userMessage = $_POST['user_message'];
    
    try {
        $response = $chatbot->processMessage($userMessage);
        
        // Loga a resposta para depuração
        if (DEBUG_MODE) {
            error_log('Resposta do chatbot: ' . $response);
        }
        
        // Garante que estamos enviando uma resposta JSON válida
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'response' => $response
        ]);
    } catch (Exception $e) {
        // Loga o erro
        error_log('Erro no chatbot: ' . $e->getMessage());
        
        // Envia resposta de erro
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage(),
            'message' => 'Ocorreu um erro ao processar sua mensagem. Por favor, tente novamente.'
        ]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Nenhuma mensagem fornecida']);
}
