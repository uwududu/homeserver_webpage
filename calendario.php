<!-- PUTA MERDA E UM CALENDARIO -->
<?php
require_once __DIR__ . '/log.php';
registrarLog($pdo, 'visita', 'Acessou calendario.php');
?>

<?php
require __DIR__ . '/config_bd.php';

$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');

$primeiroDiaSemana = (int)date('w', mktime(0, 0, 0, $mes, 1, $ano)); // domingo = 0
$totalDias = (int)date('t', mktime(0, 0, 0, $mes, 1, $ano));
$nomesMeses = ['', 'Janeiro','Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

$mesAnterior = $mes - 1; $anoAnterior = $ano;
if ($mesAnterior < 1) { $mesAnterior = 12; $anoAnterior--; }
$mesProximo = $mes + 1; $anoProximo = $ano;
if ($mesProximo > 12) { $mesProximo = 1; $anoProximo++; }

$primeiroDia = sprintf('%04d-%02d-01', $ano, $mes);
$ultimoDia = sprintf('%04d-%02d-%02d', $ano, $mes, $totalDias);

$stmt = $pdo->prepare("SELECT data, titulo FROM eventos WHERE data BETWEEN ? AND ? ORDER BY data");
$stmt->execute([$primeiroDia, $ultimoDia]);

$eventos = [];
foreach ($stmt->fetchAll() as $linha) {
    $eventos[$linha['data']][] = $linha['titulo'];
}
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="_css/style.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário</title>
</head>
<body>
    <a class="card" href="index.php" style="display:inline-block; margin-bottom:16px;">&larr; Voltar</a>
    <h1><?= $nomesMeses[$mes] . ' de ' . $ano ?></h1>

    <div style="margin-bottom:16px">
        <a class="card" href="?mes=<?= $mesAnterior ?>&ano=<?= $anoAnterior ?>">&larr; Anterior</a>
        <a class="card" href="?mes=<?= $mesProximo ?>&ano=<?= $anoProximo ?>">Proximo &rarr; </a>
                                                                                <!-- rawr  mds quero me matar depois de escrever isso-->
    </div>

    <div class="calendario">
        <?php foreach (['Dom', 'Seg', 'Ter', 'Qua','Qui', 'Sex', 'Sáb'] as $i => $dia): ?>
            <div class="cal-cabecalho <?= ($i === 0 || $i === 6) ? 'fds' : '' ?>"><?= $dia ?></div>
        <?php endforeach; ?>

        <?php for ($i = 0; $i < $primeiroDiaSemana; $i++): ?>
            <div class="cal-dia vazio"></div>
        <?php endfor; ?>

        <?php for ($dia = 1; $dia <= $totalDias; $dia++):
            $diaSemana = (int)date('w', mktime(0, 0, 0, $mes, $dia, $ano));
            $chave = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
            $eventosDoDia = $eventos[$chave] ?? [];
            $hoje = ($chave === date('Y-m-d')) ? 'hoje' : '';
        ?>
            <div class="cal-dia <?= ($diaSemana === 0 || $diaSemana === 6) ? 'fds' : '' ?> <?= $hoje ?>">
                <span class="numero"><?= $dia ?></span>
                <?php foreach ($eventosDoDia as $ev): ?>
                    <div class="evento"><?= htmlspecialchars($ev) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endfor; ?>
    </div>
</body>
</html>