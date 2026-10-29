<?php
require_once __DIR__ . '/config_bd.php';

$tipoFiltro = $_GET['tipo'] ?? '';
$dataInicio = $_GET['inicio'] ?? '';
$dataFim = $_GET['fim'] ?? '';
$ipBusca = trim($_GET['ip'] ?? '');

$condicoes = [];
$parametros = [];

if ($tipoFiltro !== '') {
    $condicoes[] = 'tipo = ?';
    $parametros[] = $tipoFiltro;
}
if ($dataInicio !== '') {
    $condicoes[] = 'criado_em >= ?';
    $parametros[] = $dataInicio . ' 00:00:00';
}
if ($dataFim !== '') {
    $condicoes[] = 'criado_em <= ?';
    $parametros[] = $dataFim . ' 23:59:59';
}
if ($ipBusca !== '') {
    $condicoes[] = 'ip LIKE ?';
    $parametros[] = '%' . $ipBusca . '%';
}

$where = $condicoes ? 'WHERE ' . implode(' AND ', $condicoes) : '';

$stmt = $pdo->prepare("SELECT * FROM logs $where ORDER BY criado_em DESC LIMIT 200");
$stmt->execute($parametros);
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<link rel="stylesheet" href="_css/style.css">
<head><meta charset="UTF-8"><title>Logs</title></head>
<body>
    <h1>Logs de acesso</h1>
    <a class="card" href="index.php" style="display:inline-block; margin-bottom:16px;">&larr; Voltar</a>

    <form method="get" class="filtro-logs">
        <select name="tipo">
            <option value="">Todos os tipos</option>
            <option value="visita" <?= $tipoFiltro === 'visita' ? 'selected' : '' ?>>Visita</option>
            <option value="clique" <?= $tipoFiltro === 'clique' ? 'selected' : '' ?>>Clique</option>
            <option value="outro" <?= $tipoFiltro === 'outro' ? 'selected' : '' ?>>Outro</option>
        </select>

        <input type="date" name="inicio" value="<?= htmlspecialchars($dataInicio) ?>">
        <input type="date" name="fim" value="<?= htmlspecialchars($dataFim) ?>">
        <input type="text" name="ip" placeholder="Buscar por IP" value="<?= htmlspecialchars($ipBusca) ?>">

        <button type="submit" class="card">Filtrar</button>
        <a href="logs.php" class="card">Limpar</a>
    </form>

    <table class="tabela-arquivos">
        <thead>
            <tr>
                <th></th>
                <th>IP</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Quando</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr><td colspan="5">Nenhum log encontrado com esse filtro.</td></tr>
            <?php endif; ?>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td>
                        <?php if ($log['pais_codigo']): ?>
                            <img src="https://flagcdn.com/24x18/<?= strtolower($log['pais_codigo']) ?>.png"
                                 alt="<?= $log['pais_codigo'] ?>" title="<?= $log['pais_codigo'] ?>">
                        <?php else: ?>
                            🏠
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($log['ip']) ?></td>
                    <td><span class="badge badge-<?= $log['tipo'] ?>"><?= $log['tipo'] ?></span></td>
                    <td><?= htmlspecialchars($log['descricao']) ?></td>
                    <td><?= date('d/m/Y H:i:s', strtotime($log['criado_em'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>