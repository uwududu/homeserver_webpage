<?php
require_once __DIR__ . '/log.php';
header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);
$descricao = $dados['descricao'] ?? 'click do adam sandler mas nao funfo e nao tem descriçao';

registrarLog($pdo, 'click', $descricao);
echo json_encode(['sucesso' => true]);
?>