<?php
require_once __DIR__ . '/../auth/Auth.php';

// Initialize authentication
$auth = new Auth();

// Redirect if already logged in
if ($auth->isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $email = $_POST['email'] ?? '';
    
    if (empty($username) || empty($password) || empty($confirmPassword) || empty($email)) {
        $error = 'Por favor, preencha todos os campos.';
    } elseif ($password !== $confirmPassword) {
        $error = 'As senhas não coincidem.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor, forneça um email válido.';
    } else {
        $userId = $auth->register($username, $password, $email);
        
        if ($userId) {
            $success = 'Registro concluído com sucesso! Você pode fazer login agora.';
        } else {
            $error = 'Nome de usuário ou email já está em uso.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - ChatFast</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
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
            width: 100%;
        }
        
        .error {
            color: #f44336;
            margin-bottom: 15px;
        }
        
        .success {
            color: #4CAF50;
            margin-bottom: 15px;
        }
        
        .links {
            text-align: center;
            margin-top: 15px;
        }
        
        .links a {
            color: #2196F3;
            text-decoration: none;
            margin: 0 10px;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro - ChatFast</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="username">Nome de Usuário</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmar Senha</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit">Registrar</button>
        </form>
        
        <div class="links">
            <a href="login.php">Já tem uma conta? Faça login</a>
        </div>
    </div>
</body>
</html>
