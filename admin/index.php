<?php
session_start();

// Configuração Rápida de Senha
$usuario_correto = "admin";
$senha_correta = "ieccp2026"; 

// Se já estiver logado, joga direto pro painel
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: painel.php');
    exit;
}

$erro = "";

// Processa o Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_post = $_POST['usuario'] ?? '';
    $senha_post = $_POST['senha'] ?? '';

    if ($usuario_post === $usuario_correto && $senha_post === $senha_correta) {
        $_SESSION['logado'] = true;
        header('Location: painel.php'); // <--- Manda para o outro arquivo
        exit;
    } else {
        $erro = "Senha incorreta!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login IECCP</title>
    <link rel="stylesheet" href="../styles/global.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background: var(--header-bg); }
        form { background: white; padding: 40px; border-radius: 10px; display: flex; flex-direction: column; gap: 15px; width: 300px; }
        input { padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        button { padding: 10px; background: var(--secondary); border: none; font-weight: bold; cursor: pointer; }
        .erro { color: red; text-align: center; }
    </style>
</head>
<body>
    <form method="POST">
        <h2 style="color: black; text-align: center;">Área Restrita</h2>
        <?php if($erro): ?><span class="erro"><?= $erro ?></span><?php endif; ?>
        <input type="text" name="usuario" placeholder="Usuário">
        <input type="password" name="senha" placeholder="Senha">
        <button type="submit">ENTRAR</button>
    </form>
</body>
</html>