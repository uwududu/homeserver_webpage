<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cassandra | Johnny Silverhand</title>
    <link rel="stylesheet" href="../_css/style.css">
    <!-- Biblioteca leve para formatar Markdown e blocos de código -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        .chat-container {
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            height: 85vh;
            background: var(--card);
            border-radius: 16px;
            border: 1px solid #2a2a38;
            overflow: hidden;
        }
        .chat-header {
            padding: 14px 20px;
            background: rgba(255, 255, 255, 0.02);
            border-bottom: 1px solid #2a2a38;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            padding: 4px 10px;
            border-radius: 20px;
            background: #15151e;
            border: 1px solid #333346;
        }
        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ff4444;
        }
        .status-dot.online {
            background: #00ff88;
            box-shadow: 0 0 8px #00ff88;
        }
        .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .btn-small {
            background: transparent;
            border: 1px solid var(--accent);
            color: var(--accent);
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            transition: 0.15s;
        }
        .btn-small:hover {
            background: var(--accent);
            color: #fff;
        }
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .msg {
            max-width: 80%;
            padding: 12px 18px;
            border-radius: 12px;
            line-height: 1.6;
            font-size: 15px;
            word-break: break-word;
        }
        .msg.user {
            align-self: flex-end;
            background: var(--accent);
            color: #fff;
            border-bottom-right-radius: 2px;
        }
        .msg.agent {
            align-self: flex-start;
            background: #20202c;
            color: var(--text);
            border-bottom-left-radius: 2px;
            border: 1px solid #333346;
        }
        .msg.agent pre {
            background: #111118;
            padding: 12px;
            border-radius: 8px;
            overflow-x: auto;
            border: 1px solid #2f2f40;
        }
        .msg.agent code {
            font-family: monospace;
            font-size: 13px;
        }
        .chat-input-area {
            display: flex;
            gap: 10px;
            padding: 16px 20px;
            background: rgba(0, 0, 0, 0.2);
            border-top: 1px solid #2a2a38;
        }
        .chat-input {
            flex: 1;
            background: #121218;
            border: 1px solid #333346;
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text);
            font-size: 15px;
            outline: none;
            resize: none;
            height: 48px;
            box-sizing: border-box;
        }
        .chat-input:focus {
            border-color: var(--accent);
        }
        .btn-send {
            background: var(--accent);
            border: none;
            border-radius: 10px;
            padding: 0 24px;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: 0.15s;
        }
        .btn-send:hover {
            opacity: 0.85;
            transform: translateY(-1px);
        }
        .btn-send:disabled {
            background: #444;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <div>
                <h2 style="margin: 0; font-size: 18px;">Cassandra Agent</h2>
                <div class="status-badge" style="margin-top: 5px;">
                    <span id="statusDot" class="status-dot"></span>
                    <span id="statusText">Verificando...</span>
                </div>
            </div>
            <div class="header-actions">
                <a class="btn-small" href="../index.php" style="text-decoration:none;">&larr; Voltar</a>
                <button id="btnStart" class="btn-small" onclick="iniciarAgente()">▶ Iniciar Cassandra</button>
                <button class="btn-small" onclick="limparChat()">Limpar Chat</button>
            </div>
        </div>

        <div class="chat-messages" id="chatBox">
            <div class="msg agent">Olá! Sou a Cassandra. Como posso ajudar no servidor hoje?</div>
        </div>

        <div class="chat-input-area">
            <textarea id="userInput" class="chat-input" placeholder="Digite uma mensagem ou comando... (Enter para enviar)"></textarea>
            <button id="sendBtn" class="btn-send" onclick="enviarMensagem()">Enviar</button>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        const userInput = document.getElementById('userInput');
        const sendBtn = document.getElementById('sendBtn');
        const statusDot = document.getElementById('statusDot');
        const statusText = document.getElementById('statusText');
        const btnStart = document.getElementById('btnStart');

        // Checa status ao carregar a página
        verificarStatus();
        setInterval(verificarStatus, 10000); // Verifica a cada 10s

        userInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                enviarMensagem();
            }
        });

        async function verificarStatus() {
            try {
                const res = await fetch('chat_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ acao: 'status' })
                });
                const data = await res.json();
                if (data.online) {
                    statusDot.className = 'status-dot online';
                    statusText.textContent = 'Cassandra Ativa';
                    btnStart.style.display = 'none';
                } else {
                    statusDot.className = 'status-dot';
                    statusText.textContent = 'Offline (Clique em Iniciar)';
                    btnStart.style.display = 'inline-block';
                }
            } catch (e) {
                statusDot.className = 'status-dot';
                statusText.textContent = 'Erro de Conexão';
            }
        }

        async function iniciarAgente() {
            btnStart.disabled = true;
            btnStart.textContent = 'Iniciando...';
            try {
                await fetch('chat_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ acao: 'iniciar' })
                });
                await verificarStatus();
            } finally {
                btnStart.disabled = false;
                btnStart.textContent = '▶ Iniciar Cassandra';
            }
        }

        async function limparChat() {
            if (!confirm('Deseja limpar o histórico da conversa?')) return;
            await fetch('chat_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ acao: 'limpar' })
            });
            chatBox.innerHTML = '<div class="msg agent">Conversa reiniciada. Como posso ajudar?</div>';
        }

        async function enviarMensagem() {
            const texto = userInput.value.trim();
            if (!texto) return;

            adicionarMensagem(texto, 'user');
            userInput.value = '';

            const msgCarregando = adicionarMensagem('Pensando...', 'agent');
            sendBtn.disabled = true;

            try {
                const response = await fetch('chat_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ acao: 'chat', mensagem: texto })
                });

                const data = await response.json();
                if (data.erro) {
                    msgCarregando.innerHTML = `<span style="color: #ff6b6b;">⚠️ ${data.erro}</span>`;
                } else {
                    // Renderiza o markdown retornado pela Cassandra
                    msgCarregando.innerHTML = marked.parse(data.resposta || '');
                }
            } catch (err) {
                msgCarregando.textContent = 'Erro ao se comunicar com a API: ' + err.message;
            } finally {
                sendBtn.disabled = false;
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        }

        function adicionarMensagem(texto, autor) {
            const div = document.createElement('div');
            div.className = `msg ${autor}`;
            div.textContent = texto;
            chatBox.appendChild(div);
            chatBox.scrollTop = chatBox.scrollHeight;
            return div;
        }
    </script>
</body>
</html>