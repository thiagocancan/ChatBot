<?php
/**
 * Arquivo de configuração do chatbot
 * 
 * Este arquivo carrega variáveis de ambiente do arquivo .env
 * e fornece a configuração para o aplicativo
 */

// Carregar variáveis de ambiente do arquivo .env
require_once __DIR__ . '/DotEnv.php';
$dotenv = new DotEnv(__DIR__ . '/.env');
$dotenv->load();

// Configuração da API Gemini
function getGeminiApiKey() {
    // Primeiro verifica a variável de ambiente (para produção/hospedagem)
    $apiKey = getenv('GEMINI_API_KEY');
    
    // Se não encontrado e estivermos em desenvolvimento, verifica uma variável de sessão
    if (!$apiKey && isset($_SESSION['GEMINI_API_KEY'])) {
        $apiKey = $_SESSION['GEMINI_API_KEY'];
    }
    
    return $apiKey;
}

// Configuração do papel da IA
function getAIRole() {
    // Primeiro verifica a variável de ambiente
    $role = getenv('AI_ROLE');
    
    // Se não encontrado, verifica uma variável de sessão
    if (!$role && isset($_SESSION['AI_ROLE'])) {
        $role = $_SESSION['AI_ROLE'];
    }
    
    // Se ainda não encontrado ou vazio, carrega de um arquivo
    if (!$role) {
        $roleFile = __DIR__ . '/ai_role.txt';
        if (file_exists($roleFile)) {
            $role = file_get_contents($roleFile);
        } else {
            $role = 'Você é um assistente de um marketplace.'; // Papel padrão vazio
        }
    }
    
    return $role;
}

/**
 * Obter o provedor de IA
 * 
 * @return string O provedor de IA (padrão: 'gemini')
 */
function getAIProvider() {
    // Primeiro verifica a variável de ambiente
    $provider = getenv('AI_PROVIDER');
    
    // Se não encontrado, verifica uma variável de sessão
    if (!$provider && isset($_SESSION['AI_PROVIDER'])) {
        $provider = $_SESSION['AI_PROVIDER'];
    }
    
    // Padrão para 'gemini' se não especificado
    return $provider ?: 'gemini';
}

/**
 * Obter a chave de API para o provedor especificado
 * 
 * @param string $provider O provedor de IA
 * @return string A chave de API
 */
function getAPIKey(string $provider) {
    switch (strtolower($provider)) {
        case 'gemini':
            return getGeminiApiKey();
        default:
            return '';
    }
}

// Configuração da aplicação
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true');
define('MAX_HISTORY', getenv('MAX_HISTORY') ? (int)getenv('MAX_HISTORY') : 10);
