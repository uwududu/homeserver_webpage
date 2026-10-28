<?php
require_once __DIR__ . '/config_bd.php';

$usuario = 'uwududu';
$senha = 'senhasuperfoda123';

$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO usuarios (usuario, senha_hash) VALUES (?, ?)");
$stmt->execute([$usuario, $hash]);

echo "usuario criado"