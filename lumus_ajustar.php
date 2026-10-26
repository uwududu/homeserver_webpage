<?php
header('Content-Type: application/json');
require_once __DIR__ . '/lumus_config.php';

$dados = json_decode(file_get_contents('php://input'), true);

$brilho = $dados['brilho'] ?? null;
$cor = $dados['cor'] ?? null;

$sucesso = definirLampada($brilho, $cor);
echo json_encode(['sucesso' => $sucesso]);