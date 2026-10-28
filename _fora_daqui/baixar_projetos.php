<?php
if($caminho === __FILE__) {
    http_response_code(403);
    exit('pode não man');
}

$arquivo = basename($caminho);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header(
    'Content-Disposition: attachment; filename="' .
    str_replace(['"', "\r", "\n"], '_', $arquivo) . '"'
);
header('Content-Length: ' . filesize($caminho));
header('X-Content-Type-Options: nosniff');

readfile($caminho);
exit;