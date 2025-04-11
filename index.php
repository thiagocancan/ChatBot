<?php

require_once 'Chatbot.php';
require_once 'config.php';
require_once 'factory/AIClientFactory.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get configuration
$aiProvider = getAIProvider();
$apiKey = getAPIKey($aiProvider);
$apiConfigured = !empty($apiKey);

if (!$apiConfigured) {
    header('Location: templates/setup.php');
    exit;
}

$aiRole = getAIRole();

// Create AI client using the factory
$aiClient = AIClientFactory::createClient($aiProvider, $apiKey);

// Create chatbot with the AI client
$chatbot = new Chatbot($aiClient, $aiRole);

if (isset($_GET['clear']) && $_GET['clear'] === 'true') {
    $chatbot->clearConversation();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gemini Chatbot</title>
    <link rel="stylesheet" href="static/css/index.css">
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div style="width:50%; margin:0 auto;">

    
        <h1>ChatFast - Chatbot de Atendimento Inteligente</h1>
        
        <div class="header-actions">
            <div class="action-buttons">
                <a href="?clear=true" class="clear-btn button">Limpar Conversa</a>
                <a href="templates/setup.php" class="settings-btn button">Configurações</a>
            </div>
        </div>
        
        <div class="chat-container" id="chat-container">
            <?php
            if (isset($_SESSION['chat_history'])) {
                foreach ($_SESSION['chat_history'] as $index => $message) {
                    if ($index === 0 && $message['role'] === 'system') {
                        continue;
                    }
                    
                    $class = $message['role'] === 'user' ? 'user-message' : 'bot-message';
                    echo "<div class='contorno'><div class='message {$class}'>{$message['content']}</div></div>";
                }
            }
            ?>
        </div>
        
        <form method="post" action="" id="chat-form">
            <div class="input-container">
                <input type="text" id="user-input" name="user_message" placeholder="Digite sua mensagem aqui..." autocomplete="off" required>
                <button type="submit">Enviar</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('chat-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const userInput = document.getElementById('user-input').value;
            if (!userInput.trim()) return;

            const chatContainer = document.getElementById('chat-container');
            chatContainer.innerHTML += `<div class='contorno'><div class="message user-message">${userInput}</div></div>`;
            
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
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.setup_required) {
                    window.location.href = 'templates/setup.php';
                    return;
                }
                
                if (data.success) {
                    chatContainer.innerHTML += `<div class='contorno'><div class="message bot-message">${data.response}</div></div>`;
                } else {
                    console.error('Error:', data.error);
                    chatContainer.innerHTML += `<div class="message bot-message">Erro: ${data.message || 'Ocorreu um erro ao processar sua mensagem.'}</div>`;
                }
            } catch (error) {
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) loadingMessage.remove();
                
                console.error('Error:', error);
                chatContainer.innerHTML += `<div class="message bot-message">Desculpe, ocorreu um erro ao processar sua solicitação. Verifique o console para mais detalhes.</div>`;
            }
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    </script>
</body>
</html>
