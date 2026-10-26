<?php
require_once __DIR__ . '/log.php';
registrarLog($pdo, 'visita', 'Acessou listar.php');
?>

<?php
$pasta = __DIR__ . '/_files/';
$itens = array_diff(scandir($pasta), ['.', '..']);

// array com as info dos arquivo da aula do marcão
$arquivos = [];
foreach ($itens as $nome) {
    $caminho = $pasta . $nome;
    $arquivos[] = [
        'nome' => $nome,
        'tamanho' => filesize($caminho),
        'modificado' => filemtime($caminho),
        'extensao' => strtolower(pathinfo($nome, PATHINFO_EXTENSION)),
    ];
}


usort($arquivos, fn($a, $b) => $b['modificado'] <=> $a['modificado']);
function formatarTamnho($bytes) {
    if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . ' GB';
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}
function iconePorExtensao($ext) {
    $imagens = ['jpg','jpeg','png','gif','webp','svg'];
    $videos = ['mp4','mkv','webm','avi','mov'];
    $documentos = ['pdf','doc','docx','txt'];
    $planilhas = ['xls','xlsx','csv'];
    $compactados = ['zip','rar','7z'];
    $site = ['php', 'html', 'css', 'js', 'sql'];

    if (in_array($ext, $imagens)) return '🖼️';
    if (in_array($ext, $videos)) return '🎬';
    if (in_array($ext, $documentos)) return '📄';
    if (in_array($ext, $planilhas)) return '📊';
    if (in_array($ext, $compactados)) return '📦';
    if (in_array($ext, $site)) return '💻';
    return '📁';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jesse Pinkman</title>
</head>
<body>
    <h1>Arquivos na pasta</h1>
    <a class="card2" href="aula_marcao.php" style="display:inline-block; margin-bottom:16px;">&larr; Voltar</a>
    

    <?php if (empty($arquivos)): ?>
        <p>nothing</p>
    <?php else: ?>
        <table class="tabela-arquivos">
            <thead>
                <tr>
                    <th>Arquivo</th>
                    <th>Tamanho</th>
                    <th>Modificado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arquivos as $arq): ?>
                    <tr>
                        <td>
                            <a href="files/<?= urlencode($arq['nome']) ?>" target="_blank" class="link-arquivo">
                                <?= iconePorExtensao($arq['extensao']) ?> <?= htmlspecialchars($arq['nome']) ?>
                            </a>
                        </td>
                        <td><?= formatarTamnho($arq['tamanho']) ?></td>
                        <td><?= date('d/m/Y H:i', $arq['modificado'])?></td>
                        <td>
                            <button class="btn-excluir" data-arquivo="<?= htmlspecialchars($arq['nome']) ?>">fodase</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    <?php endif; ?>
    <!-- meu deus a volta do id torrada 'suprised_face' -->
    <div id="toast"></div>
    <script>
        document.querySelectorAll('.btn-excluir').forEach(btn => {
            btn.addEventListener('click', async () => {
                const nome = btn.dataset.arquivo;
                if (!confirm(`Excluir "${nome}"? Essa ação não pode ser desfeita.`)) return;

                const resposta = await fetch('excluir.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json'},
                    body: JSON.stringify({ arquivo: nome})
                });
                const resultado = await resposta.json();

                if (resultado.sucesso) {
                    btn.closest('tr').remove();
                }
                else{
                    alert('erro ao excluir: ' + resultado.motivo);
                }
            });
        });
    </script>
</body>
</html>