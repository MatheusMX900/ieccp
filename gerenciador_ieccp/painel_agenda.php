<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once 'funcoes.php';

if (!isset($_COOKIE['admin_token'])) {
    header("Location: index.php");
    exit;
}
$stmt = $pdo->prepare("SELECT id FROM admins WHERE session_token = ?");
$stmt->execute([$_COOKIE['admin_token']]);
if (!$stmt->fetch()) {
    setcookie('admin_token', '', time() - 3600, '/');
    header("Location: index.php");
    exit;
}

$jsonFile = "../data/agenda.json";
$imgFolder = "../img/agenda/";

$msg = "";
$editData = null;

// CARREGAR DADOS PARA EDIÇÃO
if (isset($_GET['editar'])) {
    $data = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true);
    foreach ($data as $item) {
        if ($item['id'] == $_GET['editar']) {
            $editData = $item;
            break;
        }
    }
}

// SALVAR / ATUALIZAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true) ?? [];
    $id = !empty($_POST['id_editar']) ? $_POST['id_editar'] : time();

    // --- TRATAMENTO DE DATAS E HORAS ---
    function formatarDataParaSalvar($dataYMD)
    {
        if (!$dataYMD) return "";
        $d = DateTime::createFromFormat('Y-m-d', $dataYMD);
        return $d ? $d->format('d/m/Y') : "";
    }

    $data_inicio = formatarDataParaSalvar($_POST['data_inicio']);
    $data_fim    = formatarDataParaSalvar($_POST['data_fim']);
    $hora_inicio = $_POST['hora_inicio'] ?? '';
    $hora_fim    = $_POST['hora_fim'] ?? '';

    // Campo legado (Compatibilidade)
    $legacyDate = $data_inicio ?: ($_POST['data_antiga'] ?? date('d/m/Y'));

    // --- UPLOAD DE IMAGEM (AGORA OPCIONAL) ---
    $imgPath = $_POST['imagem_atual'] ?? ''; // Começa vazio ou com a imagem antiga

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $newJsonPath = "img/agenda/" . time() . "." . $ext;

        if (compress($_FILES['imagem']['tmp_name'], "../" . $newJsonPath)) {
            $imgPath = $newJsonPath;
            // Remove a antiga para não acumular lixo
            if (!empty($_POST['imagem_atual']) && file_exists("../" . $_POST['imagem_atual'])) {
                @unlink("../" . $_POST['imagem_atual']);
            }
        }
    }

    // --- MONTAGEM DO ARRAY ---
    $newItem = [
        "id" => $id,
        "img" => $imgPath, // Se não enviou nada, salva vazio ""
        "titulo" => filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS),
        "local" => filter_input(INPUT_POST, 'local', FILTER_SANITIZE_SPECIAL_CHARS),
        "texto" => strip_tags($_POST['texto']),

        "data_inicio" => $data_inicio,
        "hora_inicio" => $hora_inicio,
        "data_fim" => $data_fim,
        "hora_fim" => $hora_fim,

        "data" => $legacyDate
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

    if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT))) {
        $msg = "<p class='success'>✅ Evento salvo!</p>";
        $editData = null;

        if ($isNewPost) {
            $msgEvento = $newItem['data_inicio'] . " - " . $newItem['titulo'];
            enviarNotificacaoOneSignal("Novo Evento na Agenda 🗓️", $msgEvento);
        }
    } else {
        $msg = "<p class='error'>Erro ao salvar.</p>";
    }
}

// DELETAR
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
    $msg = "<p class='warning'>🗑️ Evento removido.</p>";
}

$list = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true);

// --- PREENCHIMENTO DOS INPUTS ---
$val_data_inicio = "";
$val_data_fim = "";

if ($editData) {
    $raw_inicio = $editData['data_inicio'] ?? $editData['data'] ?? '';
    if ($raw_inicio) {
        $d = DateTime::createFromFormat('d/m/Y', $raw_inicio);
        if ($d) $val_data_inicio = $d->format('Y-m-d');
    }

    $raw_fim = $editData['data_fim'] ?? '';
    if ($raw_fim) {
        $d = DateTime::createFromFormat('d/m/Y', $raw_fim);
        if ($d) $val_data_fim = $d->format('Y-m-d');
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Agenda</title>
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
        button,
        select {
            width: 100%;
            margin-bottom: 1rem;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ddd;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .grid-dates {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .date-group {
            display: flex;
            gap: 10px;
        }

        .date-group div {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #555;
        }

        small {
            font-weight: normal;
            color: #888;
        }

        button {
            background: #27ae60;
            color: white;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: 0.2s;
            margin-top: 10px;
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

        @media (max-width: 700px) {
            .grid-dates {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include 'menu_admin.php'; ?>
        <?= $msg ?>

        <h3><?= $editData ? '✏️ Editar Evento' : '📅 Novo Evento' ?></h3>

        <form method="POST" enctype="multipart/form-data" id="main-form">
            <input type="hidden" name="id_editar" value="<?= $editData['id'] ?? '' ?>">
            <input type="hidden" name="imagem_atual" value="<?= $editData['img'] ?? '' ?>">
            <input type="hidden" name="data_antiga" value="<?= $editData['data'] ?? '' ?>">

            <label>Título do Evento:</label>
            <input type="text" name="titulo" value="<?= $editData['titulo'] ?? '' ?>" required placeholder="Ex: Culto de Jovens">

            <div class="grid-dates">
                <div>
                    <label style="color:#27ae60">🟢 Início (Obrigatório)</label>
                    <div class="date-group">
                        <div>
                            <small>Data</small>
                            <input type="date" name="data_inicio" value="<?= $val_data_inicio ?>" required>
                        </div>
                        <div>
                            <small>Hora (Opcional)</small>
                            <input type="time" name="hora_inicio" value="<?= $editData['hora_inicio'] ?? '' ?>">
                        </div>
                    </div>
                </div>

                <div>
                    <label style="color:#c0392b">🔴 Fim (Opcional)</label>
                    <div class="date-group">
                        <div>
                            <small>Data</small>
                            <input type="date" name="data_fim" value="<?= $val_data_fim ?>">
                        </div>
                        <div>
                            <small>Hora</small>
                            <input type="time" name="hora_fim" value="<?= $editData['hora_fim'] ?? '' ?>">
                        </div>
                    </div>
                </div>
            </div>

            <label>📍 Local:</label>
            <input type="text" name="local" value="<?= $editData['local'] ?? '' ?>" required placeholder="Ex: Templo Principal">

            <label>📝 Descrição:</label>
            <textarea name="texto" required placeholder="Detalhes do evento..."><?= $editData['texto'] ?? '' ?></textarea>

            <label>📸 Imagem (Opcional):</label>
            <small style="color:#666">Se deixar vazio, o site usará a logo da igreja.</small>
            <input type="file" name="imagem" accept="image/*">

            <button type="submit" id="btn-submit"><?= $editData ? 'SALVAR ALTERAÇÕES' : 'PUBLICAR EVENTO' ?></button>
            <?php if ($editData): ?> <a href="painel_agenda.php"><button type="button" class="btn-cancel">CANCELAR</button></a> <?php endif; ?>
        </form>

        <div style="margin-top:40px;">
            <h3>Eventos Cadastrados</h3>
            <?php if ($list): foreach ($list as $i): ?>
                    <div class="item">
                        <div class="item-info">
                            <?php if (!empty($i['img'])): ?>
                                <img src="../<?= $i['img'] ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;">
                            <?php else: ?>
                                <div style="width:60px; height:60px; background:#ddd; border-radius:4px; display:flex; align-items:center; justify-content:center; font-size:20px;">🖼️</div>
                            <?php endif; ?>
                            <div>
                                <strong><?= $i['titulo'] ?></strong><br>
                                <small>
                                    🗓️ Início: <?= $i['data_inicio'] ?? $i['data'] ?>
                                    <?= !empty($i['hora_inicio']) ? ' às ' . $i['hora_inicio'] : '' ?>
                                </small>
                            </div>
                        </div>
                        <div class="actions">
                            <a href="?editar=<?= $i['id'] ?>" class="btn-edit">Editar</a>
                            <a href="?deletar=<?= $i['id'] ?>" class="btn-del" onclick="return confirm('Apagar?');">Excluir</a>
                        </div>
                    </div>
            <?php endforeach;
            endif; ?>
        </div>
    </div>
</body>

</html>