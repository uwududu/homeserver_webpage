<?php
header('Content-Type: application/json');

$pastaDestino = __DIR__ . '/files/';

if (!is_dir($pastaDestino)) {
    mkdir($pastaDestino, 0755, true);
}

if (!is_writable($pastaDestino)) {
    echo json_encode(['sucesso' => false, 'motivo' => 'pasta sem permissão de escrita']);
    exit;
}

if (!isset($_FILES['arquivos'])) {
    echo json_encode(['sucesso' => false, 'motivo' => 'nenhum arquivo enviado']);
    exit;
}

$nomes = $_FILES['arquivos']['name'];
$tmpNomes = $_FILES['arquivos']['tmp_name'];
$erros = $_FILES['arquivos']['error'];

$sucesso = 0;
$falhas = [];

foreach ($nomes as $i => $nomeOriginal) {
    if ($erros[$i] !== UPLOAD_ERR_OK) {
        $falhas[] = $nomeOriginal;
        continue;
    }

    $nomeSeguro = basename($nomeOriginal);
    $destino = $pastaDestino . $nomeSeguro;

    if (move_uploaded_file($tmpNomes[$i], $destino)) {
        $sucesso++;
    } else {
        $falhas[] = $nomeOriginal;
    }
}

if ($sucesso > 0 && empty($falhas)) {
    echo json_encode(['sucesso' => true, 'qtd' => $sucesso]);
} else {
    $motivo = $sucesso > 0
        ? "$sucesso enviados, falha em: " . implode(', ', $falhas)
        : 'falha em todos os arquivos';
    echo json_encode(['sucesso' => false, 'motivo' => $motivo]);
}