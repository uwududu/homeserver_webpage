<?php
header('Content-Type: application/json; charset=utf-8');

// ==========================================
// CONFIGURAÇÕES DO SERVIDOR LINUX
// ==========================================
// Altere para a pasta real onde você clonou a cassandra no seu Linux:
$CASSANDRA_DIR = '/home/SEU_USUARIO/cassandra-agent';
$CASSANDRA_PORT = 5000;
$API_BASE_URL = "http://127.0.0.1:$CASSANDRA_PORT";

// Recebe a ação solicitada pelo JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$acao = $input['acao'] ?? 'chat';

// ------------------------------------------
// 1. VERIFICAR STATUS (Online / Offline)
// ------------------------------------------
if ($acao === 'status') {
    $ch = curl_init("$API_BASE_URL/api/status");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $status = json_decode($response, true);
        echo json_encode(['online' => true, 'detalhes' => $status]);
    } else {
        echo json_encode(['online' => false]);
    }
    exit;
}

// ------------------------------------------
// 2. INICIAR O AGENTE EM SEGUNDO PLANO
// ------------------------------------------
if ($acao === 'iniciar') {
    $python = "$CASSANDRA_DIR/.venv/bin/python";
    $script = "$CASSANDRA_DIR/web_ui.py";

    // Executa em background com nohup no Linux sem travar o PHP
    $comando = "cd $CASSANDRA_DIR && nohup $python $script --port $CASSANDRA_PORT --allow-shell > /dev/null 2>&1 &";
    shell_exec($comando);

    // Aguarda 3 segundos para o FastAPI subir
    sleep(3);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Comando de inicialização enviado!']);
    exit;
}

// ------------------------------------------
// 3. LIMPAR CONVERSA
// ------------------------------------------
if ($acao === 'limpar') {
    $ch = curl_init("$API_BASE_URL/api/clear");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    curl_close($ch);

    echo json_encode(['sucesso' => true]);
    exit;
}

// ------------------------------------------
// 4. ENVIAR MENSAGEM (CHAT)
// ------------------------------------------
$mensagem = trim($input['mensagem'] ?? '');
if ($mensagem === '') {
    echo json_encode(['erro' => 'Mensagem vazia']);
    exit;
}

// Envia a mensagem para a API da Cassandra
$payload = json_encode(['message' => $mensagem]);

$ch = curl_init("$API_BASE_URL/api/chat");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120); // Modelos de IA podem demorar até 2 min

$resposta = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError || $httpCode !== 200) {
    echo json_encode([
        'erro' => 'Não foi possível se comunicar com o agente. Verifique se ele está iniciado.'
    ]);
    exit;
}

echo json_encode([
    'resposta' => trim($resposta)
]);