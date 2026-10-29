<?php
require_once __DIR__ . '/config_bd.php'


$estadoAtual = buscarEstadoLampada();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumus</title>
    <link rel="stylesheet" href="../_css/style.css">
</head>
<body>
    <a class="card" href="../index.php" style="display:inline-block; margin-bottom: 16px;">&larr; Voltar</a>
    <h1>Lumus</h1>
    <div class="lampada-warp">
        <div class="lampada-icone <?= $estadoAtual === 'on' ? 'ligada' : '' ?>" id="icone-lampada">:emoticone-lampada:</div>
        <p id="status-texto"<?= $estadoAtual === 'on' ? 'Ligada' : 'Desligada' ?>></p>
        <button id="btn-toggle" class="card2">Ligar / Desligar</button>

        <div class="controle-lampada">
            <label for="brilho">Brilho</label>
            <input type="range" id="brilho" min="0" max="255" value="255">

            <label for="cor">Cor</label>
            <input type="color" id="cor" value="#ffcc33">
        </div>
    </div>

    <script>
        const btnToggle = document.getElementById('btn-toggle');
        const inputBrilho = document.getElementById('brilho');
        const inputCor = document.getElementById('cor');
        const icone = document.getElementById('icone-lampada');
        const status = document.getElementById('status-texto');

        btnToggle.addEventListener('click', async () => {
            const resposta = await fetch('acionar_lampada.php', { method: 'POST'});
            const resultado = await resposta.json();
            if (resultado.sucesso) {
                icone.classList.toggle('ligada');
                status.textContent = icone.classList.contains('ligada') ? 'Ligada' : 'Desligada';
            }
        });
        
        //debounce: vai esperar o slider parar para mandar a requisição
        let timeoutId;
        function enviarAjuste() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(async () => {
                const hex = inputCor.value;
                const r = parseInt(hex.slice(1, 3), 16);
                const g = parseInt(hex.slice(3, 5), 16);
                const b = parseInt(hex.slice(5, 7), 16);

                await fetch ('lumus_ajustar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        brilho: parseInt(inputBrilho.value),
                        cor: [r, g, b]
                    }, 300);
                });

                icone.classList.add('ligada');
                status.textContent = 'Ligada';
                icone.style.filter = `drop-shadow(0 0 20px ${hex})`;
            })
        }

        inputBrilho.addEventListener('input', enviarAjuste);
        inputCor.addEventListener('input', enviarAjuste);
    </script>
</body>
</html>