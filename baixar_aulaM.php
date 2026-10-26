<?php
$pasta = __DIR__ . '/files/';
$nome = basename($_GET['arquivo'] ?? '');
$caminho = $pasta . $nome;

if (!file_exists($caminho)){
    http_response_code(404);
    die('tem isso n man')
}
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $nome . '"');
header('Content-Length: ' . filesize($caminho));
readfile($caminho);
exit;
?>