<?php
require_once __DIR__ . '/../../core/Controller.php';

/**
 * Authentication Controller
 */
class AuthController extends Controller {
    
    public function login() {
        // Redirect if already logged in
        if ($this->auth->isLoggedIn()) {
            $this->redirect('/');
        }
        
        $error = '';
        
        // Process login form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error = 'Por favor, preencha todos os campos.';
            } else {
                if ($this->auth->login($username, $password)) {
                    $this->redirect('/');
                } else {
                    $error = 'Nome de usuário ou senha incorretos.';
                }
            }
        }
        
        $this->view('auth/login', ['error' => $error]);
    }
    
    public function register() {
        // Redirect if already logged in
        if ($this->auth->isLoggedIn()) {
            $this->redirect('/');
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
                $userId = $this->auth->register($username, $password, $email);
                
                if ($userId) {
                    $success = 'Registro concluído com sucesso! Você pode fazer login agora.';
                } else {
                    $error = 'Nome de usuário ou email já está em uso.';
                }
            }
        }
        
        $this->view('auth/register', [
            'error' => $error,
            'success' => $success
        ]);
    }
    
    public function logout() {
        $this->auth->logout();
        $this->redirect('/auth/login');
    }
}
