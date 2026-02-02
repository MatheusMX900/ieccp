<?php
$db_path = __DIR__ . '/../database/ieccp.db';

try {
    // Cria/Conecta ao banco SQLite
    $pdo = new PDO("sqlite:" . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Se der erro, mostra na tela
    die("Erro de conexão (db.php): " . $e->getMessage());
}
