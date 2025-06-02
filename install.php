<?php
/**
 * Script de instalação
 * 
 * Este script configura a aplicação realizando:
 * 1. Criação do arquivo .env, se não existir
 * 2. Configuração do banco de dados
 */

// Verifica se o arquivo .env existe
if (!file_exists(__DIR__ . '/.env')) {
    // Copia .env.example para .env
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', __DIR__ . '/.env');
        echo "Arquivo .env criado a partir do .env.example\n";
        echo "Por favor, edite o arquivo .env com suas configurações.\n";
    } else {
        die("Arquivo .env.example não encontrado. Não foi possível criar o .env.\n");
    }
}

// Carrega as variáveis de ambiente
require_once __DIR__ . '/DotEnv.php';
$dotenv = new DotEnv(__DIR__ . '/.env');
$dotenv->load();

// Verifica se as credenciais do banco de dados estão configuradas
$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');
$dbName = getenv('DB_NAME');

if (!$dbHost || !$dbUser || !$dbName) {
    echo "A configuração do banco de dados está incompleta no arquivo .env.\n";
    echo "Por favor, atualize o arquivo .env com as credenciais do banco de dados.\n";
    exit(1);
}

// Configura o banco de dados
echo "Configurando o banco de dados...\n";
require_once __DIR__ . '/database/setup.php';

echo "\nInstalação concluída!\n";
echo "Agora você pode acessar a aplicação.\n";

