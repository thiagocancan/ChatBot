<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api_key'])) {
    // Store API key in session
    $_SESSION['GEMINI_API_KEY'] = $_POST['api_key'];
    
    // Redirect to index page
    header('Location: index.php');
    exit;
}

// Check if we should show success message
$setupComplete = isset($_GET['setup']) && $_GET['setup'] === 'complete';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração do Chatbot Gemini</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .setup-container {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        h1 {
            color: #4285F4; /* Google blue color */
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            background-color: #4285F4; /* Google blue color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #3367D6; /* Darker Google blue */
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .instructions {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .instructions h2 {
            margin-top: 0;
        }
        .instructions ol {
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <h1>Configuração do Chatbot Gemini</h1>
    
    <?php if ($setupComplete): ?>
    <div class="alert alert-success">
        Configuração concluída com sucesso! Seu chatbot está pronto para uso.
    </div>
    <?php endif; ?>
    
    <div class="setup-container">
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
        
        <form method="post" action="">
            <div class="form-group">
                <label for="api_key">Chave da API Gemini:</label>
                <input type="text" id="api_key" name="api_key" placeholder="Cole sua chave da API Gemini aqui" required>
            </div>
            <button type="submit">Salvar Configuração</button>
        </form>
    </div>
    
    <p>
        <a href="index.php">Voltar para o chatbot</a>
    </p>
</body>
</html>