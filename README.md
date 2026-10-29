# 🖥️ Home Server & NAS Dashboard

Dashboard e central de utilitários web para servidor doméstico (Home Server / NAS) baseado em Linux, PHP, MySQL e integração com Agente de IA autônomo.

---

## 📸 Recursos e Módulos

O painel reúne diferentes serviços e automações locais em uma interface unificada com tema escuro (*dark theme* neon):

* **📊 Dashboard Principal (`index.php`):**
  * Monitoramento de armazenamento em disco em tempo real (espaço livre / total).
  * Próximos eventos e compromissos sincronizados com o banco de dados.
  * Resumo financeiro integrado via Google Sheets (CSV).
  * Logs de auditoria e visitas recentes (IP, país e data/hora).
  * Cards de navegação rápida para todas as ferramentas e serviços do servidor.

* **💡 Lumus — Automação de Luzes (`_automacao_luz/`):**
  * Controle de iluminação inteligente com botões de alternância (On/Off).
  * Ajuste contínuo de intensidade/brilho ($0$ a $255$).
  * Seletor de cores RGB em tempo real.

* **🤖 Assistente de IA / Agente Local (`_chat/`):**
  * Interface conversacional estilo ChatGPT/Claude com tema escuro.
  * Integração direta com o agente autônomo **Cassandra** (`web_ui.py`).
  * Monitoramento de status em tempo real (🟢 Online / 🔴 Offline).
  * Botão na interface para inicialização em segundo plano via PHP.
  * Renderização nativa de Markdown e blocos de código com destaque de sintaxe.

* **🎬 Central de Mídia (`midia.php`):**
  * Player de vídeo HTML5 integrado com lista de reprodução automática para arquivos `.mp4` e `.webm` armazenados em `_media/`.

* **📁 Gerenciador de Arquivos & Aulas (`listar.php`, `upload.php`):**
  * Upload, gerenciamento e download de arquivos e materiais de estudo.

* **📅 Calendário & Agendamento (`calendario.php`):**
  * Visualização de eventos, datas comemorativas e lançamentos.

* **🔐 Autenticação de Usuários (`_users/`):**
  * Controle de sessões e login para restringir acesso a áreas administrativas.

---

## 🗂️ Estrutura do Repositório

```text
homeserver_webpage/
├── _automacao_luz/      # Controle IoT / Lumus (lâmpada, brilho, cores)
├── _chat/               # Interface web e API do Agente de IA (Cassandra)
│   ├── chat.php         # Interface visual estilo ChatGPT
│   └── chat_api.php     # Endpoint PHP para comunicação com a IA
├── _css/                # Folhas de estilo (tema dark neon em style.css)
├── _files/              # Diretório de arquivos enviados pelo sistema
├── _fora_daqui/         # Utilitários de download e listagem externa
├── _media/              # Armazenamento de vídeos para o player de mídia
├── _sql/                # Scripts SQL para inicialização do banco (schema.sql)
├── _users/              # Sistema de autenticação e controle de sessões
├── index.php            # Painel principal / Dashboard
├── midia.php            # Central de mídia e player de vídeo
├── calendario.php       # Calendário de eventos e datas
├── upload.php           # Upload de arquivos
└── config_bd.php        # Configuração de conexão com o banco MySQL
```

---

## ⚙️ Pré-requisitos & Tecnologias

* **Sistema Operacional:** Linux (Ubuntu / Debian / Raspberry Pi OS)
* **Web Server:** Apache 2 com `mod_rewrite` ativado
* **Linguagens:** PHP 8.x + Python 3.10+ (para o agente de IA)
* **Banco de Dados:** MySQL / MariaDB
* **Rede / Acesso Remoto:** Tailscale / SSH / IP Local

---

## 🚀 Instalação e Configuração

### 1. Clonar o Repositório
```bash
cd /var/www/
git clone https://github.com/uwududu/homeserver_webpage.git
sudo chown -R www-data:www-data homeserver_webpage
sudo chmod -R 755 homeserver_webpage
```

### 2. Configurar o Banco de Dados MySQL
Importe o arquivo `_sql/schema.sql` para criar o banco e as tabelas:
```bash
mysql -u root -p < _sql/schema.sql
```
Em seguida, configure as credenciais de acesso no arquivo `config_bd.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'home_server');
```

### 3. Configurar o Agente de IA (Opcional / Módulo Chat)
Se você for utilizar o assistente Cassandra no servidor:
```bash
# Clone o repositório do agente
git clone https://github.com/DarkyHArry/cassandra-agent.git
cd cassandra-agent

# Crie e ative o ambiente virtual
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt

# Configure suas chaves no .env
cp .env.example .env
nano .env
```
Para um tutorial de como instalar/usar o Cassandra, visite o repositorio do responsavel do projeto do cassandra-agent: 
https://github.com/DarkyHArry/cassandra-agent
No arquivo `_chat/chat_api.php`, aponte a variável `$CASSANDRA_DIR` para o caminho onde o agente foi clonado.

---

## 🎨 Personalização de Estilo
O estilo do sistema é gerenciado de forma centralizada pelo arquivo [`_css/style.css`](_css/style.css), utilizando variáveis CSS globais:
* `--bg`: Fundo escuro profundo (`#0f0f14`)
* `--card`: Superfície dos cards e blocos (`#1a1a24`)
* `--accent`: Cor de destaque / neon pink (`#ff4f9a`)
* `--text`: Cor de tipografia clara (`#eee`)
