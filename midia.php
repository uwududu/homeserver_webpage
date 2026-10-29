<?php
$pasta = __DIR__ . '/_media/';
$extensoesAceitas = ['mp4', 'webm'];

if (!is_dir($pasta)) {
    mkdir($pasta, 0755, true);
}
$itens = array_diff(scandir($pasta), ['.', '..']);
$videos = [];
foreach ($itens as $nome) {
    $ext =strtolower(pathinfo($nome, PATHINFO_EXTENSION));
    if (in_array($ext, $extensoesAceitas)) {
        $videos[] = $nome;
    }
}
sort($videos);
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="_css/style.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Mídia</title>
</head>
<body>
    <a class="card" href="index.php" style="display:inline-block; margin-bottom: 16px;">&larr; Voltar</a>
    <h1>mídia</h1>

    <?php if (empty($videos)): ?>
        <p>x = 0 videos. nao tem nada aqui patrao aqui na tua pasta <code>media</code></p>
    <?php else: ?>
        <div class="player-wrap">
            <div class="player-principal">
                <video id="player" controls></video>
                <div id="titulo-atual" class="titulo-atual"></div>
            </div>
        </div>

        <div class="playlist">
            <h2>playlist</h2>
            <input type="text" id="busca-video" placeholder="Buscar video..." class="input-busca">
            <p id="sem-resultado" class="sem-resultado">achei nada patrao</p>
            <ul id="lista-videos">
                <?php foreach ($videos as $i => $v) : 
                    $nomeExibicao = pathinfo($v, PATHINFO_FILENAME);    
                ?>
                    <li data-index="<?= $i ?>" data-nome="<?= htmlspecialchars($nomeExibicao) ?>" class="item-playlist">
                        <?= htmlspecialchars($nomeExibicao) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="playlist">
            <h2>playlist</h2>
            <ul id="lista-videos">
                <?php foreach ($videos as $i => $v): ?>
                    <li data-index="<?= $i ?>" class="item-playlist">
                        <?= htmlspecialchars(pathinfo($v, PATHINFO_FILENAME)) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <script>
        const videos = <?= json_encode($videos) ?>;
        const base = '_media/';
        const player = document.getElementById('player');
        const tituloAtual = document.getElementById('titulo-atual');
        const itens = document.querySelectorAll('.item-playlist');
        const inputBusca = document.getElementById('busca-video');
        const listaVideos = document.getElementById('lista-videos');
        const msgSemResultado = document.getElementById('sem-resultado');
        
        inputBusca.addEventListener('input', () => {
            const termo = inputBusca.value.toLowerCase().trim();
            let algumVisivel = false;

            itens.forEach(li => {
                const nomeOriginal = li.dataset.nome;
                const nomeLower = nomeOriginal.toLowerCase();
                const bate = nomeLower.includes(termo);
                
                li.style.display = bate ? '' : 'none';
                if (bate) algumVisivel = true;

                if (termo === ''){
                    li.textContent = nomeOriginal;
                }
                else if (bate) {
                    const indice = nomeLower.indexOf(termo);
                    const antes = nomeOriginal.slice(0, indice);
                    const meio = nomeOrginal.slice(indice, indice + termo.length);
                    const depois = nomeOriginal.slice(indice + termo.length);
                    li.innerHTML = `${antes}<mark>${meio}</mark>${depois}`;
                }
            });
            msgSemResultado.style.display = algumVisivel ? 'none' : 'block';
        });

        let indiceAtual = 0;

        function tocar(indice) {
            if (indice < 0 || indice >=videos.length) return;
            indiceAtual = indice;
            player.src = base + encodeURIComponent(videos[indice]);
            player.play();
            tituloAtual.textContent = videos[indice].replace(/\.[^/.]+$/, '');
            itens.forEach(li => li.classList.remove('ativo'));
            itens[indice].classList.add('ativo');
        }
        itens.forEach(li => {
            li.addEventListener('click', () => tocar(parseInt(li.dataset.index)));
        });
        player.addEventListener('ended', () => tocar(indiceAtual + 1));

        // toca automaticamente o video
        if (videos.length > 0 ) tocar(0);
    </script>
</body>
</html>