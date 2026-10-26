<?php
require_once __DIR__ . '/config_bd.php';
require_once __DIR__ . '/log.php';
registrarLog($pdo, 'visita', 'Acessou sim.php');

// proximo evento
$stmt = $pdo->prepare("SELECT data, titulo FROM eventos WHERE data >= CURDATE() ORDER BY data ASC LIMIT 1");
$stmt->execute();
$proximoEvento = $stmt->fetch();

// ultimos acessos (provavel que nao vai ter nada nunca)
$stmt = $pdo->query("SELECT ip, pais_codigo, descricao, criado_em FROM logs ORDER BY criado_em DESC LIMIT 3");
$ultimosLogs = $stmt->fetchAll();

// espaço livre (vou ter que mudar para o caminho real do disco)
$espacoLivre = disk_free_space(__DIR__);
$espacoTotal = disk_total_space(__DIR__);
$percentualUsado = round ((($espacoTotal - $espacoLivre) / $espacoTotal) * 100);

function formatarBytes($bytes) {
    if ($bytes >= 1073741824) return round ($bytes / 1073741824, 1) . ' GB';
    if ($bytes >= 1048576) return round ($bytes / 1048576, 1) . ' MB';
    return round($bytes / 1024, 1) . ' KB';
}

function buscarResumoGastos () {
    $csv = @file_get_contents(planilha_resumo);
    if ($csv === false) return null;
    $linhas = array_map('str_getcsv', explode("\n", trim($csv)));
    $resumo = [];
    foreach ($linhas as $linha) {
        if (count($linha) >= 2) {
            $resumo[$linha[0]] = $linha[1];
        }
    }
    return $resumo;
}
$gastos = buscarResumoGastos();
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="_css/aaaa.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Walter Hartwell White</title>
</head>
<body>
    <h1>Random bs</h1>
    <section class="dashboard">
        <div class="dash-card">
            <h3>Next Event (bullshit)</h3>
            <?php if ($proximoEvento): ?>
                <p class="dash-destaque"><?= htmlspecialchars($proximoEvento['titulo']) ?></p>
                <p class="dash-sub"><?= date('d/m/Y'), strtotime($proximoEvento['data']) ?></p>
            <?php else: ?>
                <p class="dash-sub">nada marcado patrao</p>
            <?php endif; ?>
        </div>

        <div class="dash-card">
            <h3>ultimos acessos</h3>
            <?php foreach ($ultimosLogs as $log): ?>
                <p class="dash-log">
                    <?php if ($log['pais_codigo']): ?>
                        <img src="https://flagcdn.com/16x12/<?= strtolower($log['pais_codigo']) ?>.png" alt="">
                    <?php else: ?>
                        emoticone do house
                    <?php endif ?>
                    <?= htmlspecialchars($log['descricao']) ?>
                    <span class="dash-sub"><?= date('H:i', strtotime($log['criado_em'])) ?></span>
                </p>
            <?php endforeach; ?>
        </div>

        <div class="dash-card">
            <h3>espaço em disco</h3>
            <div class="barra-progresso">
                <div class="barra-preenchida" style="width: <?= $percentualUsado ?>%"></div>
            </div>
            <p class="dash-sub"><?= formatarBytes($espacoLivre) ?> livres de <?= formatarBytes($espacoTotal) ?></p>
        </div>

        <div class="dash-card">
            <h3>gastos do mes</h3>
            <?php if ($gastos): ?>
                <p class="dash-destaque">R$ <?= htmlspecialchars($gastos['Total Geral'] ?? '0') ?></p>
                <p class="dash-sub">
                    Assinaturas: R$ <?= htmlspecialchars($gastos['Total Assinaturas'] ?? '0') ?>
                    . Variáveis: R$ <?= htmlspecialchars($gastos['Total Variáveis'] ?? '0') ?>
                </p>
            <?php else: ?>
                <p class="dash-sub">não consigo carregar a planilha jefe, estoy cansado</p>
            <?php endif; ?>
        </div>
    </section>
    
        <section>
            <h2>Links</h2>
                <div class="grid">
                    <a class="card" href="https://youtube.com">Youtube</a>
                    <a class="card" href="https://github.com">Github</a>
                    <a class="card" href="https://store.steampowered.com">Steam</a>
                    <a class="card" href="https://mail.proton.me/u/1/inbox">ProtonMail</a>
                    <a class="card" href="https://protonvpn.com/download">ProtonVPN</a>
                    <a class="card" href="https://drive.proton.me/login">ProtonDrive</a>
                    <a class="card" href="https://claude.ai/new">Claudio</a>
                    <a class="card" href="https://skynetchat.net">SkynetChat</a>
                    <a class="card" href="https://travle.earth">Travle (geografia)</a>
                    <a class="card" href="https://chatgpt.com">GPT</a>
                    <a class="card" href="https://www.hltv.org/">CS</a>
                </div>
        </section>
        <br><br>
        <section>
            <h2>Botões Principais</h2>
                <div class="grid2">
                    <a class="card2" href="#">nao sei</a>
                    <a class="card2" href="aula_marcao.php">aula marcão</a>
                    <a class="card2" href="calendario.php">um fucking calendario irmao</a>
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
</body>
</html>