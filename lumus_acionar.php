<?php
header('Content-type: application/json');
require_once __DIR__ . '/lumus_config.php';

$sucesso = chamarHomeAssistant('toggle');
echo json_encode(['sucesso' => $sucesso]);