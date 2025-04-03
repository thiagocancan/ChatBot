<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load config to get default role
require_once '../config.php';
$defaultRole = getAIRole();

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store API key in session if provided
    if (isset($_POST['api_key']) && !empty($_POST['api_key'])) {
        $_SESSION['GEMINI_API_KEY'] = $_POST['api_key'];
    }
    
    // Store AI role in session if provided
    if (isset($_POST['ai_role'])) {
        $_SESSION['AI_ROLE'] = $_POST['ai_role'];
        
        // Clear chat history to apply new role
        $_SESSION['chat_history'] = [];
    }
    
    // Redirect to index page
    header('Location: ../index.php');
    exit;
}

// Check if we should show success message
$setupComplete = isset($_GET['setup']) && $_GET['setup'] === 'complete';

// Get current API key if exists
$currentApiKey = '';
if (isset($_SESSION['GEMINI_API_KEY'])) {
    $currentApiKey = $_SESSION['GEMINI_API_KEY'];
}

// Get current AI role if exists
$currentRole = '';
if (isset($_SESSION['AI_ROLE'])) {
    $currentRole = $_SESSION['AI_ROLE'];
} else {
    $currentRole = $defaultRole;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração do Chatbot Gemini</title>
    <link rel="stylesheet" href="../static/css/setup.css">
</head>
<body>
    <h1>Configuração do Chatbot Gemini</h1>
    
    <?php if ($setupComplete): ?>
    <div class="alert alert-success">
        Configuração concluída com sucesso! Seu chatbot está pronto para uso.
    </div>
    <?php endif; ?>
    
    <div class="tab">
        <button class="tablinks active" onclick="openTab(event, 'ApiKeyTab')">Chave da API</button>
        <button class="tablinks" onclick="openTab(event, 'RoleTab')">Papel da IA</button>
    </div>
    
    <form method="post" action="">
        <div id="ApiKeyTab" class="tabcontent" style="display: block;">
            <div class="instructions">
                <h2>Como obter uma chave de API do Google Gemini</h2>
                <ol>
                    <li>Acesse o <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a></li>
                    <li>Faça login com sua conta Google</li>
                    <li>Clique em "Get API key" ou "Create API key"</li>
                    <li>Copie a chave gerada</li>
                    <li>Cole a chave no campo abaixo</li>
                </ol>
            </div>
            
            <div class="form-group">
                <label for="api_key">Chave da API Gemini:</label>
                <input type="text" id="api_key" name="api_key" placeholder="Cole sua chave da API Gemini aqui" value="<?php echo htmlspecialchars($currentApiKey); ?>">
            </div>
        </div>
        
        <div id="RoleTab" class="tabcontent">
            <div class="instructions">
                <h2>Definir o Papel da IA</h2>
                <p>Aqui você pode definir como a IA deve se comportar. Este texto será usado como uma instrução inicial para orientar as respostas da IA.</p>
                <p>Para um bot de atendimento de marketplace, recomendamos incluir:</p>
                <ul>
                    <li>Descrição do papel (atendente de marketplace)</li>
                    <li>Tipos de perguntas que pode responder (produtos, pedidos, devoluções, etc.)</li>
                    <li>Tom de voz (cordial, profissional, etc.)</li>
                    <li>Limitações (não inventar informações sobre produtos específicos)</li>
                </ul>
            </div>
            
            <div class="form-group">
                <label for="ai_role">Papel da IA:</label>
                <textarea id="ai_role" name="ai_role" placeholder="Defina o papel da IA aqui..."><?php echo htmlspecialchars($currentRole); ?></textarea>
            </div>
        </div>
        
        <button type="submit">Salvar Configuração</button>
    </form>
    
    <p>
        <a href="../index.php">Voltar para o chatbot</a>
    </p>
    
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            evt.currentTarget.className += " active";
        }
    </script>
</body>
</html>