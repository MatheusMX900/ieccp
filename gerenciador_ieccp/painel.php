<?php
session_start();
require_once 'funcoes.php'; 

$jsonFile = "../data/noticias.json";
$imgFolder = "../img/noticias/";
$timeout = 1800;

if (isset($_SESSION['ultima_atividade']) && (time() - $_SESSION['ultima_atividade'] > $timeout)) {
    session_unset(); session_destroy(); header('Location: /?erro=expirado'); exit;
}
$_SESSION['ultima_atividade'] = time();
if (empty($_SESSION['logado'])) { header('Location: /'); exit; }

$msg = "";
$editData = null;

// Load Edit
if (isset($_GET['editar'])) {
    $data = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true);
    foreach ($data as $item) {
        if ($item['id'] == $_GET['editar']) {
            $editData = $item;
            break;
        }
    }
}

// Save / Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true) ?? [];
    $id = $_POST['id_editar'] ?? time();
    
    $imgPath = $_POST['imagem_atual'] ?? '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $newJsonPath = "img/noticias/" . time() . "." . $ext;
        
        if (compress($_FILES['imagem']['tmp_name'], "../" . $newJsonPath)) {
            $imgPath = $newJsonPath;
            if (!empty($_POST['imagem_atual']) && file_exists("../" . $_POST['imagem_atual'])) {
                @unlink("../" . $_POST['imagem_atual']);
            }
        }
    }

    $newItem = [
        "id" => $id,
        "img" => $imgPath,
        "titulo" => filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS),
        "texto" => strip_tags($_POST['texto']),
        "data" => $_POST['data_original'] ?? date('d/m/Y')
    ];

    $updated = false;
    foreach ($data as $k => $v) {
        if ($v['id'] == $id) {
            $data[$k] = $newItem;
            $updated = true;
            break;
        }
    }

    if (!$updated) {
        if (empty($imgPath)) {
            $msg = "<p class='error'>⛔ Imagem obrigatória para novas notícias.</p>";
        } else {
            array_unshift($data, $newItem);
            $updated = true; // Force save
        }
    }

    if ($updated && empty($msg)) {
        if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT))) {
            $msg = "<p class='success'>✅ Salvo com sucesso!</p>";
            $editData = null;
        } else {
            $msg = "<p class='error'>Erro ao salvar arquivo JSON.</p>";
        }
    }
}

// Delete
if (isset($_GET['deletar'])) {
    $data = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true);
    $newData = [];
    foreach ($data as $item) {
        if ($item['id'] == $_GET['deletar']) {
            if (file_exists("../" . $item['img'])) @unlink("../" . $item['img']);
        } else {
            $newData[] = $item;
        }
    }
    file_put_contents($jsonFile, json_encode($newData, JSON_PRETTY_PRINT));
    $msg = "<p class='warning'>🗑️ Item removido.</p>";
}

$list = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Notícias</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600&display=swap" rel="stylesheet">
    <style>
        body { padding: 20px; background: #ecf0f1; font-family: 'Poppins', sans-serif; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        input, textarea, button { width: 100%; margin-bottom: 1rem; padding: 12px; border-radius: 6px; border: 1px solid #ddd; box-sizing: border-box; }
        textarea { height: 120px; resize: vertical; }
        button { background: #27ae60; color: white; font-weight: 600; cursor: pointer; border: none; transition: 0.2s; }
        button:hover { background: #219150; }
        button:disabled { background: #95a5a6; cursor: wait; opacity: 0.8; }
        .btn-cancel { background: #95a5a6; margin-top: 5px; }
        
        .item { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #eee; align-items: center; }
        .item-info { display: flex; gap: 15px; align-items: center; }
        .actions { display: flex; gap: 10px; }
        .btn-edit, .btn-del { padding: 8px 15px; text-decoration: none; border-radius: 4px; font-size: 0.9rem; color: white; }
        .btn-edit { background: #f39c12; } .btn-del { background: #e74c3c; }
        .success { color: #27ae60; background: #e8f5e9; padding: 10px; border-radius: 4px; }
        .error { color: #c0392b; background: #fadbd8; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'menu_admin.php'; ?>
        <?= $msg ?>

        <h3><?= $editData ? '✏️ Editar Notícia' : '➕ Nova Notícia' ?></h3>

        <form method="POST" enctype="multipart/form-data" id="main-form">
            <input type="hidden" name="id_editar" value="<?= $editData['id'] ?? '' ?>">
            <input type="hidden" name="imagem_atual" value="<?= $editData['img'] ?? '' ?>">
            <input type="hidden" name="data_original" value="<?= $editData['data'] ?? '' ?>">

            <label>Título:</label> <input type="text" name="titulo" value="<?= $editData['titulo'] ?? '' ?>" required>
            <label>Texto:</label> <textarea name="texto" required><?= $editData['texto'] ?? '' ?></textarea>
            
            <label>Imagem:</label>
            <?php if($editData): ?> <small style="color:#666">(Vazio para manter atual)</small> <?php endif; ?>
            <input type="file" name="imagem" accept="image/*" <?= $editData ? '' : 'required' ?>>
            
            <button type="submit" id="btn-submit"><?= $editData ? 'SALVAR' : 'PUBLICAR' ?></button>
            <?php if($editData): ?> <a href="painel.php"><button type="button" class="btn-cancel">CANCELAR</button></a> <?php endif; ?>
        </form>

        <div style="margin-top:40px;">
            <h3>Publicados</h3>
            <?php if ($list): foreach ($list as $i): ?>
                <div class="item">
                    <div class="item-info">
                        <?php if($i['img']): ?> <img src="../<?= $i['img'] ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;"> <?php endif; ?>
                        <div><strong><?= $i['titulo'] ?></strong><br><small><?= $i['data'] ?></small></div>
                    </div>
                    <div class="actions">
                        <a href="?editar=<?= $i['id'] ?>" class="btn-edit">Editar</a>
                        <a href="?deletar=<?= $i['id'] ?>" class="btn-del" onclick="return confirm('Apagar?');">Excluir</a>
                    </div>
                </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
    <script>
        document.getElementById('main-form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            btn.innerHTML = '⏳ Processando...';
            btn.style.cursor = 'wait';
            setTimeout(() => btn.disabled = true, 10);
        });
    </script>
</body>
</html>