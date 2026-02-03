<?php
require_once __DIR__ . '/../includes/db.php';

echo "<h1>Ferramenta de Configuração</h1>";

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario TEXT NOT NULL UNIQUE,
        senha TEXT NOT NULL,
        session_token TEXT,
        ultimo_acesso DATETIME,
        ip_ultimo_acesso TEXT,
        user_agent TEXT,
        tentativas_falhas INTEGER DEFAULT 0
    )");
    echo " Passo 1: Tabela 'admins' verificada com sucesso.<br>";

    $novo_user = 'asdhaiusdiaisudnijashda';
    $nova_pass = 'asidhaisldauisgdauisbd';

    // Verifica se já existe
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE usuario = ?");
    $stmt->execute([$novo_user]);

    if ($stmt->fetch()) {
        // Se existir, atualiza a senha
        $hash = password_hash($nova_pass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE admins SET senha = ? WHERE usuario = ?")->execute([$hash, $novo_user]);
        echo " Passo 2: Usuário 'admin' já existia. Senha ATUALIZADA para: 123456<br>";
    } else {
        // Se não existir, cria
        $hash = password_hash($nova_pass, PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admins (usuario, senha) VALUES (?, ?)")->execute([$novo_user, $hash]);
        echo " Passo 2: Usuário 'admin' CRIADO com a senha: 123456<br>";
    }

    echo "<hr><h3 style='color:green'>Tudo pronto!</h3>";
    echo "<a href='../gerenciador_ieccp/index.php'>CLIQUE AQUI PARA LOGAR</a>";
} catch (PDOException $e) {
    echo "Erro Fatal: " . $e->getMessage();
}
