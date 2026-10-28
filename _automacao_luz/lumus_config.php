<?php
require_once __DIR__ . '/config_bd.php';

define('HA_URL', 'http://'); // ip da tailscale do raspberry
define('HA_TOKEN', 'MEU_TOKEN');
define('HA_ENTIDADE', 'light.lampada_quarto');
function chamarHomeAssistant($servico) {
    $url = HA_URL . "/api/services/light/{$servico}";
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['entity_id' => HA_ENTIDADE]),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . HA_TOKEN,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 5,
    ]);
    $resposta = curl_exec($ch);
    $erro = curl_errno($ch);
    curl_close($ch);
    
    return !$erro;
}

// mostra o status da lampada atual (ligada ou desligada)
function buscarEstadoLampada() {
    $ch = curl_init(HA_URL . "/api/states" . HA_ENTIDADE);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['entity_id' => HA_TOKEN]),
        CURLOPT_TIMEOUT => 5,
    ]);
    $resposta = curl_exec($ch);
    curl_close($ch);

    $dados = json_decode($resposta, true);
    return $dados['state'] ?? 'desconhecido'; // 'on' ou 'off'
}

// serviço para conseguir mudar a cor da luz
function definirLampada($brilho = null, $corRgb = null) {
    $dados = ['entity_id' => HA_ENTIDADE];

    if ($brilho !== null) {
        $dados['brightness'] = (int)$brilho;
    }
    if ($corRgb !== null) {
        $dados['rgb_color'] = $corRgb;
    }

    $ch = curl_init(HA_URL . "/api/services/light/turn_on");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . HA_TOKEN,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 5,
    ]);

    $resposta = curl_exec($ch);
    $erro = curl_errno($ch);
    curl_close($ch);

    return !$erro;
}