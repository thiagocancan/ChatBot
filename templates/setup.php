<?php
require_once '../config.php';
require_once '../auth/Auth.php';

// Initialize authentication
$auth = new Auth();

// Check if user is admin
if (!$auth->isLoggedIn() || !$auth->isAdmin()) {
    header('Location: ../index.php');
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['api_key']) && !empty($_POST['api_key'])) {
        $_SESSION['GEMINI_API_KEY'] = $_POST['api_key'];
        
        if (isset($_POST['ai_role']) && !empty($_POST['ai_role'])) {
            $_SESSION['AI_ROLE'] = $_POST['ai_role'];
            
            // Save AI role to file
            file_put_contents('../ai_role.txt', $_POST['ai_role']);
        }
        
        if (isset($_POST['ai_provider']) && !empty($_POST['ai_provider'])) {
            $_SESSION['AI_PROVIDER'] = $_POST['ai_provider'];
        }
        
        $message = 'Configurações salvas com sucesso!';
        $success = true;
        
        // Redirect after 2 seconds
        header('refresh:2;url=../index.php');
    } else {
        $message = 'Por favor, forneça uma chave de API válida.';
    }
}

// Get current values
$currentApiKey = isset($_SESSION['GEMINI_API_KEY']) ? $_SESSION['GEMINI_API_KEY'] : '';
$currentAiRole = getAIRole();
$currentAiProvider = getAIProvider();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração do Chatbot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        textarea {
            height: 200px;
            resize: vertical;
        }
        
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Configuração do Chatbot</h1>
        
        <?php if ($message): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="api_key">Chave da API Gemini:</label>
                <input type="text" id="api_key" name="api_key" value="<?php echo htmlspecialchars($currentApiKey); ?>" required>
                <small>Obtenha sua chave em <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a></small>
            </div>
            
            <div class="form-group">
                <label for="ai_provider">Provedor de IA:</label>
                <select id="ai_provider" name="ai_provider">
                    <option value="gemini" <?php echo $currentAiProvider === 'gemini' ? 'selected' : ''; ?>>Google Gemini</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="ai_role">Papel do Assistente:</label>
                <textarea id="ai_role" name="ai_role"><?php echo htmlspecialchars($currentAiRole); ?></textarea>
                <small>Defina o comportamento e as instruções para o assistente.</small>
            </div>
            
            <button type="submit">Salvar Configurações</button>
        </form>
        
        <a href="../index.php" class="back-link">Voltar para o Chat</a>
    </div>
</body>
</html>
