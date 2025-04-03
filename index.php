<?php
// Entry point for the application
require_once 'Chatbot.php';
require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if API key is configured
$apiKey = getGeminiApiKey();
$apiConfigured = !empty($apiKey);

// If API key is not configured, redirect to setup page
if (!$apiConfigured) {
    header('Location: setup.php');
    exit;
}

// Initialize the chatbot with the Gemini API key
$chatbot = new Chatbot($apiKey);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatFast</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .chat-container {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            height: 400px;
            overflow-y: auto;
            background-color: #fff;
        }
        .chat-container::-webkit-scrollbar {
            width: 8px;
        }
        .chat-container::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }
        .chat-container::-webkit-scrollbar-thumb:hover {
            background: #bbb;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .user-message {
            background-color: #d0ebff;
            text-align: right;
            color: #000;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
            margin-left: auto;
        }
        .bot-message {
            background-color: #f1f1f1;
            color: #333;
            padding: 10px;
            border-radius: 10px;
            max-width: 70%;
        }
        .input-container {
            display: flex;
            gap: 10px;
        }
        #user-input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .settings-link {
            text-align: right;
            margin-bottom: 10px;
        }
        .settings-link a {
            color: #4CAF50;
            text-decoration: none;
        }
        .settings-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>ChatFast - Chatbot de Atendimento Inteligente</h1>
    
    <div class="settings-link">
        <!-- Link to settings page 
        <a href="setup.php">Configurações</a>
        -->
    </div>
    
    <div class="chat-container" id="chat-container">
        <?php
        // Display chat history if available
        if (isset($_SESSION['chat_history'])) {
            foreach ($_SESSION['chat_history'] as $message) {
                $class = $message['role'] === 'user' ? 'user-message' : 'bot-message';
                echo "<div class='message {$class}'>{$message['content']}</div>";
            }
        }
        ?>
    </div>
    
    <form method="post" action="" id="chat-form">
        <div class="input-container">
            <input type="text" id="user-input" name="user_message" placeholder="Digite sua mensagem aqui..." required>
            <button type="submit">Enviar</button>
        </div>
    </form>

    <script>
        // Simple JavaScript to handle form submission without page reload
        document.getElementById('chat-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const userInput = document.getElementById('user-input').value;
            if (!userInput.trim()) return;
            
            // Add user message to chat
            const chatContainer = document.getElementById('chat-container');
            chatContainer.innerHTML += `<div class="message user-message">${userInput}</div>`;
            
            // Clear input field
            document.getElementById('user-input').value = '';
            
            // Show loading indicator
            chatContainer.innerHTML += `<div class="message bot-message" id="loading-message">Pensando...</div>`;
            
            // Send request to PHP backend
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
                    window.location.href = 'setup.php';
                    return;
                }
                
                if (data.success) {
                    chatContainer.innerHTML += `<div class="message bot-message">${data.response}</div>`;
                } else {
                    console.error('Error:', data.error);
                    chatContainer.innerHTML += `<div class="message bot-message">Erro: ${data.message || 'Ocorreu um erro ao processar sua mensagem.'}</div>`;
                }
            } catch (error) {
                console.error('Error:', error);
                chatContainer.innerHTML += `<div class="message bot-message">Desculpe, ocorreu um erro ao processar sua solicitação. Verifique o console para mais detalhes.</div>`;
            }
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    </script>
</body>
</html>