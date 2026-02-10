<?php
$db = __DIR__ . '../database/ieccp.db';
require_once '../includes/db.php';
require_once '../gerenciador_ieccp/funcoes.php';

date_default_timezone_set('America/Sao_Paulo');
header('Content-Type: text/html; charset=utf-8');

try {
    $sqlTabela = "
        CREATE TABLE IF NOT EXISTS presente_diario (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            data_publicacao TEXT UNIQUE NOT NULL,
            titulo TEXT,
            referencia_bilbica TEXT,
            versiculo_chave TEXT,
            conteudo TEXT,
            autor TEXT,
            frase_destaque TEXT,
            importado_em DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
    $pdo->exec($sqlTabela);
} catch (PDOException $e) {
    die("Erro ao criar tabela: " . $e->getMessage());
}

// Baixar os dados da RTM
$anoAtual = date('Y');
$hoje = date('Y-m-d');
$json = "https://presentediario.rtmbrasil.org.br/js/{$anoAtual}.json";

echo "Iniciando verificação para: <strong>$hoje</strong>...<br>";

$stmt = $pdo->prepare("SELECT COUNT(*) FROM presente_diario WHERE data_publicacao = :data");
$stmt->execute([':data' => $hoje]);
$existe = $stmt->fetchColumn();

if ($existe > 0) {
    die("A devocional de hoje já está salvo no banco. Nenhuma ação necessária");
}

echo "Baixando dados da RTM... <br>";
$conteudo = @file_get_contents($json);

if (!$conteudo) {
    die("Erro ao acessar o site da RTM. Verifique se está tudo certo! URL registrado: $json");
}

$mensagens = json_decode($conteudo, true);

$msgHoje = null;
foreach ($mensagens as $msg) {
    if (isset($msg['publishedAt']) && $msg['publishedAt'] === $hoje) {
        $msgHoje = $msg;
        break;
    }
}

if (!$msgHoje) {
    die("JSON baixado, mas não encontrei a mensagem de hoje ($hoje)");
}

// Salvar no banco
try {
    $sqlInsert = "INSERT INTO presente_diario 
        (data_publicacao, titulo, referencia_bilbica, versiculo_chave, conteudo, autor, frase_destaque, imagem, audio, youversionLink) 
        VALUES 
        (:data, :titulo, :ref, :verso, :conteudo, :autor, :frase, :img, :audio, :youversion)";

    $stmt = $pdo->prepare($sqlInsert);
    $stmt->execute([
        ':data'     => $msgHoje['publishedAt'],
        ':titulo'   => $msgHoje['title'],
        ':ref'      => $msgHoje['reference'],
        ':verso'    => $msgHoje['keyVerse'],
        ':conteudo' => $msgHoje['content'],
        ':autor'    => $msgHoje['author'],
        ':frase'    => $msgHoje['excerpt'],
        ':img'      => $msgHoje['imageUrl'],
        ':audio'    => $msgHoje['audioUrl'],
        ':youversion' => $msgHoje['youversionLink']
    ]);

    enviarNotificacaoOneSignal("Novo Presente Diário!", $msgHoje['title']);

    echo "Sucesso! Devocional '{$msgHoje['title']}' foi salvo.";
} catch (PDOException $e) {
    echo "❌ Erro ao gravar no banco: " . $e->getMessage();
}
