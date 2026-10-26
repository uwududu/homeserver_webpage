<?php
header('Content-Type: application/json');

$pasta = __DIR__ . '/files/';
$dados = json_decode(file_get_contents('php://input'), true);
$nome = basename($dados['arquivo'] ?? '');

$caminho = $pasta . $nome;

if ($nome === '' || !file_exists($caminho)) {
    echo json_decode(['sucesso' => false, 'motivo' => 'arquivo n encontrado']);
    exit;
}
if (unlink($caminho)) {
    echo json_encode(['sucesso' => true])
}
else {
    echo json_encode(['sucesso' => false, 'motivo' => sem permissao para excluir])
}
?>