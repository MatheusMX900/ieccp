<?php
// --- MODO DE DEPURAÇÃO (Desative ao publicar) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 1. IMPORTA A FUNÇÃO DE COMPRESSÃO (Aqui está o segredo!)
// O painel chama a função 'compress', sem saber se ela usa GD ou TinyPNG.
require_once 'funcoes.php'; 

// 2. CONFIGURAÇÕES
$tempoLimite = 1800; // 30 minutos
$arquivoJson = '../data/noticias.json';
$pastaImagens = '../img/noticias/';

// 3. SEGURANÇA (TIMEOUT E LOGIN)
if (isset($_SESSION['ultima_atividade']) && (time() - $_SESSION['ultima_atividade'] > $tempoLimite)) {
    session_unset(); session_destroy();
    header('Location: index.php?erro=expirado'); exit;
}
$_SESSION['ultima_atividade'] = time();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header('Location: index.php'); exit;
}

$mensagem = "";

// 4. LÓGICA DE UPLOAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem'])) {
    
    // Nome único + .jpg (O TinyPNG pode converter para JPG se quisermos, ou manter original)
    // Aqui vou manter a extensão original para ser mais flexível com o TinyPNG
    $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $nomeImagem = time() . "." . $extensao; 
    
    $caminhoFinalServidor = $pastaImagens . $nomeImagem;
    $caminhoParaOJson = "../data/noticias.json" . $nomeImagem;
    
    $arquivoTemporario = $_FILES['imagem']['tmp_name'];

    // TENTA COMPRIMIR E SALVAR
    if (compress($arquivoTemporario, $caminhoFinalServidor)) {
        
        $conteudoAtual = file_exists($arquivoJson) ? file_get_contents($arquivoJson) : '[]';
        $arrayNoticias = json_decode($conteudoAtual, true);
        if (!is_array($arrayNoticias)) $arrayNoticias = [];

        $novaNoticia = [
            "id" => time(),
            "img" => $caminhoParaOJson,
            "titulo" => filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS),
            "texto" => filter_input(INPUT_POST, 'texto', FILTER_SANITIZE_SPECIAL_CHARS),
            "data" => date('d/m/Y')
        ];

        array_unshift($arrayNoticias, $novaNoticia);
        
        if (file_put_contents($arquivoJson, json_encode($arrayNoticias, JSON_PRETTY_PRINT))) {
            $mensagem = "<p style='color: green; font-weight:bold;'>✅ Sucesso!</p>";
        } else {
            $mensagem = "<p style='color: red;'>❌ Erro ao salvar JSON.</p>";
        }
    } else {
        $mensagem = "<p style='color: red; font-weight:bold;'>⛔ Erro!</p>";
    }
}

// 5. LÓGICA DE EXCLUSÃO
if (isset($_GET['deletar'])) {
    $id = $_GET['deletar'];
    $dados = json_decode(file_exists($arquivoJson) ? file_get_contents($arquivoJson) : '[]', true);
    
    foreach ($dados as $item) {
        if ($item['id'] == $id) {
            if (file_exists("../" . $item['img'])) unlink("../" . $item['img']);
            break;
        }
    }

    $novoArray = array_values(array_filter($dados, fn($n) => $n['id'] != $id));
    file_put_contents($arquivoJson, json_encode($novoArray, JSON_PRETTY_PRINT));
    $mensagem = "<p style='color: orange;'>🗑️ Notícia apagada.</p>";
}

$lista = json_decode(file_exists($arquivoJson) ? file_get_contents($arquivoJson) : '[]', true);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel IECCP</title>
    <link rel="stylesheet" href="../styles/global.css">
    <style>
        body { padding: 40px; background: #1c1c1c; font-family: sans-serif; }
        .container { color:#333 !important; max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        input, textarea, button { width: 100%; margin-bottom: 15px; padding: 10px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        input, textarea { background: #fff; color: #333; border: 1px solid #ccc; }
        textarea { height: 100px; }
        button { background: #27ae60; color: white; font-weight: bold; cursor: pointer; border: none; }
        button:hover { background: #219150; }
        .item { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #eee; align-items: center; }
        .item strong { font-size: 1.1rem; color: #000 !important;}
        .btn-del { background: #e74c3c; width: auto; padding: 5px 15px; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
            <h2>Gerenciar Notícias</h2>
            <a href="logout.php" style="color:red; text-decoration:none; font-weight:bold;">Sair</a>
        </div>
        <?= $mensagem ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Título:</label> <input type="text" name="titulo" required>
            <label>Texto:</label> <textarea name="texto" required></textarea>
            <label>Imagem (JPG/PNG):</label> <input type="file" name="imagem" accept="image/*" required>
            <button type="submit">PUBLICAR</button>
        </form>

        <div style="margin-top:40px;">
            <h3>Publicados (<?= is_array($lista) ? count($lista) : 0 ?>)</h3>
            <?php if (is_array($lista)): foreach ($lista as $n): ?>
                <div class="item">
                    <div style="display:flex; gap:10px; align-items:center;">
                        <?php if(isset($n['img'])): ?><img src="../<?= $n['img'] ?>" width="50" height="50" style="object-fit:cover; border-radius:4px;"><?php endif; ?>
                        <strong><?= $n['titulo'] ?></strong>
                    </div>
                    <a href="?deletar=<?= $n['id'] ?>" class="btn-del" onclick="return confirm('Apagar?');">Excluir</a>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</body>
</html>