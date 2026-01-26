<?php
// depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$usuario = "admin";
$senha_hash = '$2y$12$lYqicsZjdVJPCA50GW4Ea.3CIjaurNXBNNOC7p/JC6IRAfBASb5kq'; // Hash gerado para "adminieccp2026"

if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {
    header('Location: painel.php');
    exit;
}

$erro = "";

// Processa o Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_post = $_POST['usuario'] ?? '';
    $senha_post = $_POST['senha'] ?? '';

    if ($usuario == $usuario_post && password_verify($senha_post, $senha_hash)) {
        $_SESSION['logado'] = true;
        $_SESSION['ultimo_acesso'] = time(); 
        header('Location: painel.php');
        exit;
    } else {
        sleep(5);
        $erro = "Senha incorreta! Aguarde alguns segundos e tente novamente.";
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
        .erro { color: red; text-align: center; font-size: 0.9rem; }
        .aviso { color: #e67e22; text-align: center; font-size: 0.9rem; font-weight: bold; }
    </style>
</head>
<body>
    <form method="POST">
        <h2 style="color: black; text-align: center;">Área Restrita</h2>
        
        <?php if (isset($_GET['erro']) && $_GET['erro'] == 'expirado'): ?> 
            <span class="aviso">Sessão expirada. Logue novamente.</span>
        <?php endif; ?>

        <?php if($erro): ?>
            <span class="erro"><?= $erro ?></span>
        <?php endif; ?>

        <input type="text" name="usuario" placeholder="Usuário">
        <input type="password" name="senha" placeholder="Senha">
        <button type="submit">ENTRAR</button>
    </form>
</body>
</html>