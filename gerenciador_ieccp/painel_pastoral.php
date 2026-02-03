<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once 'funcoes.php';

// 1. SEGURANÇA
if (!isset($_COOKIE['admin_token'])) {
    header("Location: /gerenciador_ieccp/");
    exit;
}
$stmt = $pdo->prepare("SELECT id FROM admins WHERE session_token = ?");
$stmt->execute([$_COOKIE['admin_token']]);
if (!$stmt->fetch()) {
    setcookie('admin_token', '', time() - 3600, '/');
    header("Location: /gerenciador_ieccp/");
    exit;
}

// 2. CONFIGURAÇÕES E CRIAÇÃO DE PASTAS (CORREÇÃO CRÍTICA)
$jsonFile = "../data/pastoral.json";
$imgFolder = "../img/pastoral/";

// Garante que as pastas existem. Se não existirem, o PHP cria.
if (!is_dir(dirname($jsonFile))) mkdir(dirname($jsonFile), 0777, true);
if (!is_dir($imgFolder)) mkdir($imgFolder, 0777, true);

$msg = "";
$editData = null;

// 3. CARREGAR DADOS (COM PROTEÇÃO CONTRA ERRO)
if (isset($_GET['editar'])) {
    $conteudo = file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]';
    $data = json_decode($conteudo, true);
    if (is_array($data)) {
        foreach ($data as $item) {
            if ($item['id'] == $_GET['editar']) {
                $editData = $item;
                break;
            }
        }
    }
}

// 4. SALVAR / ATUALIZAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conteudo = file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]';
    $data = json_decode($conteudo, true);

    // Se o arquivo estiver corrompido, inicia um array limpo para não travar
    if (!is_array($data)) $data = [];

    $id = !empty($_POST['id_editar']) ? $_POST['id_editar'] : time();

    // Lógica da Imagem
    $imgPath = $_POST['imagem_atual'] ?? '';
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $newJsonPath = "img/pastoral/" . time() . "." . $ext;

        // Tenta comprimir. Se der erro, salva normal.
        if (function_exists('compress') && compress($_FILES['imagem']['tmp_name'], "../" . $newJsonPath)) {
            $imgPath = $newJsonPath;
        } else {
            // Fallback: move o arquivo sem comprimir se a função falhar
            move_uploaded_file($_FILES['imagem']['tmp_name'], "../" . $newJsonPath);
            $imgPath = $newJsonPath;
        }

        // Apaga a antiga
        if (!empty($_POST['imagem_atual']) && file_exists("../" . $_POST['imagem_atual'])) {
            @unlink("../" . $_POST['imagem_atual']);
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
    $isNewPost = false;

    foreach ($data as $k => $v) {
        if ($v['id'] == $id) {
            $data[$k] = $newItem;
            $updated = true;
            break;
        }
    }

    if (!$updated) {
        array_unshift($data, $newItem);
        $isNewPost = true;
    }

    // AQUI ESTÁ O SEGREDO: JSON_UNESCAPED_UNICODE
    $jsonSalvo = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    if ($jsonSalvo && file_put_contents($jsonFile, $jsonSalvo, LOCK_EX)) {
        $msg = "<p class='success'>✅ Publicado com sucesso!</p>";
        $editData = null;

        // Notificação
        if ($isNewPost) {
            enviarNotificacaoOneSignal("Nova Palavra Pastoral 📖", $newItem['titulo']);
        }
    } else {
        $msg = "<p class='error'>Erro ao salvar. Verifique permissões ou caracteres inválidos.</p>";
    }
}

// 5. DELETAR
if (isset($_GET['deletar'])) {
    $data = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true);
    if (is_array($data)) {
        $newData = [];
        foreach ($data as $item) {
            if ($item['id'] == $_GET['deletar']) {
                if (file_exists("../" . $item['img'])) @unlink("../" . $item['img']);
            } else {
                $newData[] = $item;
            }
        }
        file_put_contents($jsonFile, json_encode($newData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        $msg = "<p class='warning'>🗑️ Item removido.</p>";
    }
}

// Recarrega a lista segura
$conteudo = file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]';
$list = json_decode($conteudo, true) ?? [];
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Pastoral</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600&display=swap" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background: #ecf0f1;
            font-family: 'Poppins', sans-serif;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        input,
        textarea,
        button {
            width: 100%;
            margin-bottom: 1rem;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            box-sizing: border-box;
        }

        textarea {
            height: 120px;
            resize: vertical;
        }

        button {
            background: #27ae60;
            color: white;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }

        button:hover {
            background: #219150;
        }

        .btn-cancel {
            background: #95a5a6;
            margin-top: 5px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 15px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .item-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn-edit,
        .btn-del {
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            color: white;
        }

        .btn-edit {
            background: #f39c12;
        }

        .btn-del {
            background: #e74c3c;
        }

        .success {
            color: #27ae60;
            background: #e8f5e9;
            padding: 10px;
            border-radius: 4px;
        }

        .error {
            color: #c0392b;
            background: #fadbd8;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include 'menu_admin.php'; ?>
        <?= $msg ?>

        <h3><?= $editData ? '✏️ Editar Pastoral' : '➕ Novo Artigo Pastoral' ?></h3>

        <form method="POST" enctype="multipart/form-data" id="main-form">
            <input type="hidden" name="id_editar" value="<?= $editData['id'] ?? '' ?>">
            <input type="hidden" name="imagem_atual" value="<?= $editData['img'] ?? '' ?>">
            <input type="hidden" name="data_original" value="<?= $editData['data'] ?? '' ?>">

            <label>Título:</label> <input type="text" name="titulo" value="<?= $editData['titulo'] ?? '' ?>" required>
            <label>Texto:</label> <textarea name="texto" required><?= $editData['texto'] ?? '' ?></textarea>

            <label>Imagem (Opcional):</label>
            <input type="file" name="imagem" accept="image/*">

            <button type="submit" id="btn-submit"><?= $editData ? 'SALVAR' : 'PUBLICAR' ?></button>
            <?php if ($editData): ?> <a href="painel_pastoral.php"><button type="button" class="btn-cancel">CANCELAR</button></a> <?php endif; ?>
        </form>

        <div style="margin-top:40px;">
            <h3>Publicados</h3>
            <?php if (!empty($list)): foreach ($list as $i): ?>
                    <div class="item">
                        <div class="item-info">
                            <?php if (!empty($i['img'])): ?>
                                <img src="../<?= $i['img'] ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;">
                            <?php else: ?>
                                <div style="width:60px; height:60px; background:#eee; border-radius:4px;"></div>
                            <?php endif; ?>
                            <div><strong><?= $i['titulo'] ?></strong><br><small><?= $i['data'] ?></small></div>
                        </div>
                        <div class="actions">
                            <a href="?editar=<?= $i['id'] ?>" class="btn-edit">Editar</a>
                            <a href="?deletar=<?= $i['id'] ?>" class="btn-del" onclick="return confirm('Apagar?');">Excluir</a>
                        </div>
                    </div>
                <?php endforeach;
            else: ?>
                <p style="text-align:center; color:#777;">Nenhum pastoral encontrado.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>