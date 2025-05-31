document.addEventListener("DOMContentLoaded", () => {
    const iaUsos = 348;
    const acessosHoje = 89;
    const historicoMensagens = [
      "Usuário: Qual é o horário de atendimento?",
      "Bot: Estamos disponíveis 24h!",
      "Usuário: Como posso acessar o dashboard?",
      "Bot: Clique no menu superior em 'Dashboard'."
    ];
  
    document.getElementById("ia-count").textContent = iaUsos;
    document.getElementById("access-count").textContent = acessosHoje;
  
    const ul = document.getElementById("mensagens");
    historicoMensagens.forEach(msg => {
      const li = document.createElement("li");
      li.textContent = msg;
      ul.appendChild(li);
    });
  });
  