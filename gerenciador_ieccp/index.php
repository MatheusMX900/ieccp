<?php
session_start();

require_once __DIR__ . '/../includes/db.php';

$erro_login = "";

// Se já estiver logado, manda pro painel
if (isset($_COOKIE['admin_token'])) {
    header("Location: painel");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_form = $_POST['usuario'] ?? '';
    $senha_form   = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE usuario = :u LIMIT 1");
    $stmt->execute(['u' => $usuario_form]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($senha_form, $admin['senha'])) {
        // Login Sucesso
        $token = bin2hex(random_bytes(32));

        $sql = "UPDATE admins SET session_token = :t, ultimo_acesso = datetime('now') WHERE id = :id";
        $pdo->prepare($sql)->execute(['t' => $token, 'id' => $admin['id']]);

        // Cookie válido por 24h
        setcookie('admin_token', $token, time() + 86400, '/', '', false, true);

        header("Location: painel");
        exit;
    } else {
        $erro_login = "Usuário ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login Admin - IECCP</title>
    <link rel="icon" type="image/svg+xml" href="/../img/ico.svg" />

    <style>
        body {
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #2c3e50;
        }

        form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            width: 300px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px;
            background: #e67e22;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #d35400;
        }

        .erro {
            color: red;
            text-align: center;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <form action="" method="POST">
        <h2 style="text-align:center; color:#333">Painel IECCP</h2>

        <?php if ($erro_login): ?>
            <div class="erro"><?= $erro_login ?></div>
        <?php endif; ?>

        <input type="text" name="usuario" placeholder="Usuário" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">ENTRAR</button>
    </form>
</body>

</html>