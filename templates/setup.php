<?php
require_once '../config.php';

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
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../static/css/setup.css">
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
