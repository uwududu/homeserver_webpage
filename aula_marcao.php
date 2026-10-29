<?php
require_once __DIR__ . '/log.php';
registrarLog($pdo, 'visita', 'Acessou aula_marcao.php');
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="_css/style.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heisenberg</title>
</head>
<body>
    <h1>Aula do Marcão</h1>
    <a class="voltarHome" href="index.php">Voltar</a>
    <section>
        <h2>Links</h2>
        <div class="grid">
            <a class="card" href="https://snack.expo.dev/">Snack</a>
            <a class="card" href="https://github.com">Github</a>
        </div>
    </section>

    <!-- parte do relogio -->
        <div id="relogio"></div>
        <script>
            function atualizarRelogio(){
                const agora = new Date();
                const hora = agora.toLocaleTimeString('pt-BR', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });

                const data = agora.toLocaleDateString('pt-BR', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
                document.getElementById('relogio').innerHTML = `${hora}<br><span class="data">${data}</span>`;
            }
            atualizarRelogio();
            setInterval(atualizarRelogio, 1000);
        </script>

    <br><br>

    <section>
        <h2>Botões Principais</h2>
        <div class="grid2">
            <div class="card2" id="btnUpload">Enviar Arquivo(s)</div>
            <input type="file" id="arquivo" name="arquivos[]" multiple hidden>

            <a class="card2" href="listar.php">Ver Arquivos</a>
        </div>
    </section>

    <div id="toast"></div>

    <script>
        const btnUpload = document.getElementById('btnUpload');
        const inputArquivo = document.getElementById('arquivo');

        btnUpload.addEventListener('click', () => inputArquivo.click());

        inputArquivo.addEventListener('change', async () => {
            if (inputArquivo.files.length === 0) return;

            const formData = new FormData();
            for (const file of inputArquivo.files) {
                formData.append('arquivos[]', file);
            }

            try {
                const resposta = await fetch('upload.php', { method: 'POST', body: formData });
                const resultado = await resposta.json();
                mostrarToast(resultado);
            } catch (erro) {
                mostrarToast({ sucesso: false, motivo: 'erro de conexão com o servidor' });
            }

            inputArquivo.value = ''; // limpa a seleção pra poder mandar de novo depois
        });

        function mostrarToast(resultado) {
            const toast = document.getElementById('toast');
            if (resultado.sucesso) {
                toast.textContent = `${resultado.qtd} arquivo(s) enviado(s) com sucesso!`;
                toast.className = 'show sucesso';
            } else {
                toast.textContent = 'Erro ao enviar: ' + resultado.motivo;
                toast.className = 'show erro';
            }
            setTimeout(() => toast.classList.remove('show'), 4000);
        }
    </script>
</body>
</html>