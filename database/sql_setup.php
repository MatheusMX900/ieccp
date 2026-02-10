<?php
require_once __DIR__ . '/../includes/db.php';

echo "<h1>ok</h1>";

try {
    // $pdo->exec("CREATE TABLE IF NOT EXISTS presente_diario (
    //         id INTEGER PRIMARY KEY AUTOINCREMENT,
    //         data_publicacao TEXT UNIQUE NOT NULL,
    //         titulo TEXT,
    //         referencia_bilbica TEXT,
    //         versiculo_chave TEXT,
    //         conteudo TEXT,
    //         autor TEXT,
    //         frase_destaque TEXT,
    //         imagem TEXT,
    //         audio TEXT,
    //         youversionLink TEXT,
    //         importado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    //     )");

    $pdo->exec("DELETE FROM presente_diario");
} catch (PDOException $e) {
    echo "Erro Fatal: " . $e->getMessage();
}
