<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | Assistente Virtual</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #e8f0ff, #f4f9ff);
      color: #2c3e50;
    }
    
    header {
      background: linear-gradient(to right, #3b82f6, #2563eb);
      color: white;
      padding: 20px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    
    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      padding: 40px;
      max-width: 1200px;
      margin: auto;
    }
    
    .card {
      background: white;
      border-left: 6px solid #3b82f6;
      border-radius: 14px;
      padding: 25px 30px;
      box-shadow: 0 6px 16px rgba(59, 130, 246, 0.1);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      animation: fadeIn 0.5s ease;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(59, 130, 246, 0.15);
    }
    
    .card h2 {
      font-size: 1.3rem;
      color: #2563eb;
      margin-bottom: 10px;
    }
    
    .value {
      font-size: 2.8rem;
      font-weight: bold;
      color: #1e3a8a;
    }
    
    .desc {
      color: #6b7280;
      font-size: 0.95rem;
    }
    
    .full-width {
      grid-column: 1 / -1;
    }
    
    #mensagens {
      list-style: none;
      margin-top: 15px;
      max-height: 240px;
      overflow-y: auto;
      padding-right: 5px;
    }
    
    #mensagens li {
      padding: 12px 18px;
      margin-bottom: 10px;
      background: #eff6ff;
      border-left: 4px solid #3b82f6;
      border-radius: 10px;
      font-size: 0.95rem;
      transition: background 0.3s ease;
    }
    
    #mensagens li:hover {
      background: #dbeafe;
    }
    
    footer {
      background: #1e3a8a;
      color: white;
      text-align: center;
      padding: 16px;
      margin-top: 40px;
      box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .back-link {
      display: inline-block;
      margin: 20px;
      padding: 10px 20px;
      background-color: #3b82f6;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s;
    }
    
    .back-link:hover {
      background-color: #2563eb;
    }
    
    .user-stats {
      margin-top: 15px;
    }
    
    .user-stats-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .message-time {
      color: #6b7280;
      font-size: 0.8rem;
      margin-top: 5px;
    }
    
    .message-user {
      font-weight: bold;
      color: #2563eb;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: scale(0.98);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Dashboard Analítico</h1>
  </header>

  <a href="<?php echo $basePath; ?>/" class="back-link">← Voltar para o Chat</a>

  <main class="dashboard">
    <section class="card" id="ia-uso">
      <h2>Uso da IA</h2>
      <p class="value" id="ia-count"><?php echo array_sum(array_column($messageStats, 'message_count')); ?></p>
      <span class="desc">Total de respostas geradas</span>
      
      <div class="user-stats">
        <?php foreach ($messageStats as $stat): ?>
        <div class="user-stats-item">
          <span><?php echo htmlspecialchars($stat['username']); ?></span>
          <span><?php echo $stat['message_count']; ?> mensagens</span>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="card" id="acessos">
      <h2>Acessos Diários</h2>
      <p class="value" id="access-count"><?php echo $accessCount; ?></p>
      <span class="desc">Número de acessos hoje</span>
    </section>

    <section class="card full-width" id="historico">
      <h2>Histórico de Mensagens</h2>
      <ul id="mensagens">
        <?php foreach ($recentMessages as $message): ?>
          <li>
            <span class="message-user"><?php echo htmlspecialchars($message['username']); ?> (<?php echo $message['role'] === 'user' ? 'Usuário' : 'IA'; ?>):</span>
            <?php echo htmlspecialchars(substr($message['content'], 0, 150)) . (strlen($message['content']) > 150 ? '...' : ''); ?>
            <div class="message-time"><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></div>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </main>

  <footer>
    <p>&copy; <?php echo date('Y'); ?> Assistente Virtual</p>
  </footer>
</body>
</html>
