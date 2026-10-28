<?php
$base = realpath(__DIR__);
$subpasta  =$_GET['p'] ?? '';

$caminho = realpath(
    $base . DIRECTORY_SEPARATOR . $subpasta
);

if (
    $caminho === false ||
    !is_dir($caminho) ||
    ($caminho !== $base  &&
        !str_starts_with(
            $caminho,
            $base . DIRECTORY_SEPARATOR
        ))
) {
    http_response_code(404);
    exit('Pasta não existente')
}

$relativoAtual = $caminho === $base
    ? ''
    : substr($caminho, strlen($base) + 1);

$itens = array_diff(scandir($caminho), ['.', '..']);
$pastas = [];
$arquivos = [];

foreach ($itens as $nome) {
    $itemCaminho = $caminho . DIRECTORY_SEPARATOR . $nome;

    if(is_link($itemCaminho)) {
        continue;
    }

    if (is_dir($itemCaminho)) {
        $pastas[] = $nome;
    }
    elseif (is_file($itemCaminho)) {
        $arquivos[] = $nome;
    }
}

natcasesort($pastas);
natscasesort($arquivos);

function h($valor){
    return htmlspecialchars(
        $valor,
        ENT_QUOTES,
        'UTF-8'
    );
}

function tamanhoArquivo($bytes){
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    }
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}

function icone($nome, $pasta = false) {
    if($pasta) return 'pasta-emoticon';
    $ext = strtolower(pathinfo($nome, PATHINFO_EXTENSION));
     return match ($ext) {
        'php', 'html', 'css', 'js' => '💻',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg' => '🖼️',
        'pdf' => '📄',
        'zip', 'rar', '7z' => '📦',
        'sql', 'txt', 'md' => '📝',
        default => '📄',
    };
}

function urlPasta($relativo) {
    return '?' . http_build_query(['p' => $relativo]);
}

function urlArquivo($arquivo) {
    return http_build_query(['arquivo' => $arquivo]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
</head>
<body>
     <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            padding: 0 18px;
            background: #f5f5f5;
            color: #222;
        }

        h1 { overflow-wrap: anywhere; }

        .lista {
            background: white;
            border-radius: 10px;
            padding: 8px 18px;
        }

        .item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }

        .item:last-child { border-bottom: none; }

        .nome {
            flex: 1;
            min-width: 150px;
            overflow-wrap: anywhere;
        }

        a { color: #1769aa; }

        .acao {
            padding: 7px 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            text-decoration: none;
            background: #fff;
        }

        .vazio { color: #666; padding: 15px 0; }
    </style>
</head>
<body>

    <h1>📂 Meus Projetos</h1>

    <p>
        <a href="?">🏠 Pasta principal</a>
        <?php if ($relativoAtual !== ''): ?>
            / <?= h($relativoAtual) ?>
        <?php endif; ?>
    </p>

    <?php if ($relativoAtual !== ''): ?>
        <?php $pai = dirname($relativoAtual); ?>
        <p>
            <a href="<?= h(urlPasta($pai === '.' ? '' : $pai)) ?>">
                ⬅ Voltar uma pasta
            </a>
        </p>
    <?php endif; ?>

    <div class="lista">
        <?php foreach ($pastas as $nome): ?>
            <?php
                $relativo = $relativoAtual === ''
                    ? $nome
                    : $relativoAtual . '/' . $nome;
            ?>

            <div class="item">
                <span><?= icone($nome, true) ?></span>

                <a class="nome"
                   href="<?= h(urlPasta($relativo)) ?>">
                    <?= h($nome) ?>/
                </a>
            </div>
        <?php endforeach; ?>

        <?php foreach ($arquivos as $nome): ?>
            <?php
                $relativo = $relativoAtual === ''
                    ? $nome
                    : $relativoAtual . '/' . $nome;

                $arquivoUrl = urlArquivo($relativo);
                $data = filemtime(
                    $caminho . DIRECTORY_SEPARATOR . $nome
                );
                $tamanho = filesize(
                    $caminho . DIRECTORY_SEPARATOR . $nome
                );
            ?>

            <div class="item">
                <span><?= icone($nome) ?></span>

                <div class="nome">
                    <strong><?= h($nome) ?></strong><br>
                    <small>
                        <?= h(tamanhoArquivo($tamanho)) ?>
                        · <?= date('d/m/Y H:i', $data) ?>
                    </small>
                </div>

                <a class="acao"
                   href="<?= h($relativo) ?>"
                   target="_blank"
                   rel="noopener">
                    Abrir
                </a>

                <a class="acao"
                   href="baixar_projeto.php?<?= h($arquivoUrl) ?>">
                    Baixar
                </a>
            </div>
        <?php endforeach; ?>

        <?php if (empty($pastas) && empty($arquivos)): ?>
            <p class="vazio">Esta pasta está vazia.</p>
        <?php endif; ?>
    </div>
</body>
</html>