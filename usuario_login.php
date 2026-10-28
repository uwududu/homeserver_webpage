<?php
session_start();
require_once __DIR__ . '/config_bd.php';

$erro = ''

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $dadosUsuario = $stmt->fetch();

    if ($dadosUsuario && password_verify($senha, $dadosUsuario['senha_hash'])) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $dadosUsuario['usuario'];
        header('Location: sim.php');
        exit;
    }
    else{
        $erro = 'usuario ou senha errado';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THe Ballad of Heisenberg</title>
</head>
<body>
    <div class="login-wrap">
        <h1>Login</h1>
        <?php if ($erro): ?>
            <p class="login-erro"><?= htmlspecialchars($erro) ?></p>
        <?php endif; ?>
        <form method="post" class="login-form">
            <input type="text" name="usuario" placeholder="Usuario" required autofocus>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit" class="card2">Entrar </button>
        </form>
    </div>
</body>
</html>