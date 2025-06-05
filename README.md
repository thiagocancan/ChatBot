# ChatFast - Chatbot de Atendimento Inteligente

![ChatFast Logo](https://via.placeholder.com/150x150.png?text=ChatFast)

## Sobre o Projeto

ChatFast é um sistema de chatbot inteligente desenvolvido em PHP que utiliza APIs de modelos de linguagem avançados (como Google Gemini) para fornecer respostas automatizadas aos usuários. O sistema implementa conceitos de Programação Orientada a Objetos, com foco em polimorfismo para permitir a fácil integração de diferentes provedores de IA.

## Objetivo e Importância

### Objetivo
O objetivo principal do ChatFast é fornecer uma plataforma de atendimento automatizado que possa ser facilmente personalizada para diferentes contextos de negócio. O sistema permite que empresas ou organizações ofereçam suporte inicial através de um chatbot inteligente, reduzindo a carga sobre equipes de atendimento humano.

### Importância
- **Redução de custos operacionais**: Automatiza o primeiro nível de atendimento ao cliente
- **Disponibilidade 24/7**: Oferece suporte contínuo sem limitações de horário
- **Escalabilidade**: Atende múltiplos usuários simultaneamente sem degradação de serviço
- **Personalização**: Permite adaptar o comportamento do chatbot para diferentes contextos
- **Aprendizado**: Demonstra a aplicação prática de conceitos avançados de POO como polimorfismo

## Tecnologias Utilizadas

- PHP 7.4+
- MySQL/MariaDB
- HTML5, CSS3, JavaScript
- APIs de IA (Google Gemini, com suporte a expansão para outras)
- Padrões de Projeto (Singleton, Factory, Strategy)

## Requisitos do Sistema

- Servidor web (Apache, Nginx)
- PHP 7.4 ou superior
- MySQL 5.7+ ou MariaDB 10.3+
- Extensões PHP: PDO, cURL, JSON
- Chave de API para o Google Gemini ou outro provedor suportado

## Instalação

### 1. Clone o repositório
```bash
git clone https://github.com/thiagocancan/ChatBot.git
cd chatbot
```

### 2. Configure o arquivo .env

Copie o arquivo de exemplo e edite com suas configurações:

```shellscript
cp .env.example .env
```

Edite o arquivo `.env` com as informações do seu banco de dados e chave de API:

### 3. Execute o script de instalação

```shellscript
php install.php
```

Este script irá:

- Criar o banco de dados se não existir
- Configurar as tabelas necessárias
- Criar um usuário administrador padrão


**IMPORTANTE**: Anote a senha do administrador que será exibida no console após a execução do script.

### 4 Defina o papel da IA

- Crie um arquivo chamado ai_role.txt no diretorio do projeto
- Dentro do arquivo txt defina o texto é ser usado como papel para a IA

### 4 Inicialize o sistema

Usando o Xampp ou da forma que preferir, execute o apache e o mysql.

O sistema estará disponível em http://localhost/chatbot/.
