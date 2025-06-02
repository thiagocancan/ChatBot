<?php
// Redirecionar para a página de login se não estiver logado
require_once 'auth/Auth.php';

$auth = new Auth();

// Se não estiver logado, redirecionar para a página de login
if (!$auth->isLoggedIn()) {
    header('Location: auth/login.php');
    exit;
}

require_once 'Chatbot.php';
require_once 'config.php';
require_once 'factory/AIClientFactory.php';

// Obter configurações
$aiProvider = getAIProvider();
$apiKey = getAPIKey($aiProvider);
$apiConfigured = !empty($apiKey);

if (!$apiConfigured) {
    header('Location: templates/setup.php');
    exit;
}

$aiRole = getAIRole();
$userId = $auth->getUserId();
$username = $auth->getUsername();
$isAdmin = $auth->isAdmin();

// Criar cliente de IA usando a fábrica
$aiClient = AIClientFactory::createClient($aiProvider, $apiKey);

// Criar chatbot com o cliente de IA, papel e ID do usuário
$chatbot = new Chatbot($aiClient, $aiRole, $userId);

// Lidar com a limpeza da conversa
if (isset($_GET['clear']) && $_GET['clear'] === 'true') {
    $chatbot->clearConversation();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatFast - Chatbot de Atendimento Inteligente</title>
    <link rel="stylesheet" href="static/css/index.css">
</head>
<body>
    <h1>ChatFast - Chatbot de Atendimento Inteligente</h1>
    
    <div class="header-actions">
        <div class="user-info">
            <span>Olá, <?php echo htmlspecialchars($username); ?>!</span>
            <a href="auth/logout.php" class="button logout-btn">Sair</a>
        </div>
        <div class="action-buttons">
            <a href="?clear=true" class="clear-btn button">Limpar Conversa</a>
            <?php if ($isAdmin): ?>
            <a href="templates/setup.php" class="settings-btn button">Configurações</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="chat-container" id="chat-container">
        <?php
        $conversation = $chatbot->getConversation();
        foreach ($conversation as $index => $message) {
            if ($index === 0 && $message['role'] === 'system') {
                continue;
            }
            
            $class = $message['role'] === 'user' ? 'user-message' : 'bot-message';
            echo "<div class='message {$class}'>{$message['content']}</div>";
        }
        ?>
    </div>
    
    <form method="post" action="" id="chat-form">
        <div class="input-container">
            <input type="text" id="user-input" name="user_message" placeholder="Digite sua mensagem aqui..." autocomplete="off" required>
            <button type="submit">Enviar</button>
        </div>
    </form>

    <script>
        document.getElementById('chat-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const userInput = document.getElementById('user-input').value;
            if (!userInput.trim()) return;

            const chatContainer = document.getElementById('chat-container');
            chatContainer.innerHTML += `<div class="message user-message">${userInput}</div>`;
            
            document.getElementById('user-input').value = '';
            
            chatContainer.innerHTML += `<div class="message bot-message" id="loading-message">Pensando...</div>`;
            
            const formData = new FormData();
            formData.append('user_message', userInput);
            
            try {
                const response = await fetch('process.php', {
                    method: 'POST',
                    body: formData
                });
                
                document.getElementById('loading-message').remove();
                
                if (!response.ok) {
                    throw new Error(`Erro HTTP! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.setup_required) {
                    window.location.href = 'templates/setup.php';
                    return;
                }
                
                if (data.success) {
                    chatContainer.innerHTML += `<div class="message bot-message">${data.response}</div>`;
                } else {
                    console.error('Erro:', data.error);
                    chatContainer.innerHTML += `<div class="message bot-message">Erro: ${data.message || 'Ocorreu um erro ao processar sua mensagem.'}</div>`;
                }
            } catch (error) {
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) loadingMessage.remove();
                
                console.error('Erro:', error);
                chatContainer.innerHTML += `<div class="message bot-message">Desculpe, ocorreu um erro ao processar sua solicitação. Verifique o console para mais detalhes.</div>`;
            }
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    </script>
</body>
</html>
