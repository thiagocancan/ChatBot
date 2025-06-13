<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatFast - Chatbot de Atendimento Inteligente</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/static/css/index.css">
</head>
<body>
    <h1>ChatFast - Chatbot de Atendimento Inteligente</h1>
    
    <div class="header-actions">
        <div class="user-info">
            <span>Olá, <?php echo htmlspecialchars($username); ?>!</span>
            <a href="<?php echo $basePath; ?>/auth/logout" class="button logout-btn">Sair</a>
        </div>
        <div class="action-buttons">
            <a href="<?php echo $basePath; ?>/?clear=true" class="clear-btn button">Limpar Conversa</a>
            <?php if ($isAdmin): ?>
            <a href="<?php echo $basePath; ?>/admin/setup" class="settings-btn button">Configurações</a>
            <a href="<?php echo $basePath; ?>/admin/dashboard" class="dashboard-btn button">Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="chat-container" id="chat-container">
        <?php
        foreach ($conversation as $index => $message) {
            if ($index === 0 && $message['role'] === 'system') {
                continue;
            }
            
            $class = $message['role'] === 'user' ? 'user-message' : 'bot-message';
            echo "<div class='message {$class}'>" . htmlspecialchars($message['content']) . "</div>";
        }
        ?>
    </div>
    
    <form method="post" action="<?php echo $basePath; ?>/process" id="chat-form">
        <div class="input-container">
            <input type="text" id="user-input" name="user_message" placeholder="Digite sua mensagem aqui..." autocomplete="off" required>
            <button type="submit">Enviar</button>
        </div>
    </form>

    <script>
        const basePath = '<?php echo $basePath; ?>';
        
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
                const response = await fetch(basePath + '/process', {
                    method: 'POST',
                    body: formData
                });
                
                document.getElementById('loading-message').remove();
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.setup_required) {
                    window.location.href = basePath + '/admin/setup';
                    return;
                }
                
                if (data.success) {
                    chatContainer.innerHTML += `<div class="message bot-message">${data.response}</div>`;
                } else {
                    console.error('Error:', data.error);
                    chatContainer.innerHTML += `<div class="message bot-message">Erro: ${data.message || 'Ocorreu um erro ao processar sua mensagem.'}</div>`;
                }
            } catch (error) {
                const loadingMessage = document.getElementById('loading-message');
                if (loadingMessage) loadingMessage.remove();
                
                console.error('Error:', error);
                chatContainer.innerHTML += `<div class="message bot-message">Desculpe, ocorreu um erro ao processar sua solicitação.</div>`;
            }
            
            chatContainer.scrollTop = chatContainer.scrollHeight;
        });
    </script>
</body>
</html>
