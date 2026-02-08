<?php
include 'funcoes.php';
verificiarEventosExpirados('../data/agenda.json');
$agenda = json_decode(file_get_contents('../data/agenda.json'), true);
?>

<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Verificação de segurança
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

// --- FUNÇÃO MÁGICA: OTIMIZAR IMAGEM (WEBP + RESIZE) ---
function uploadOtimizado($file, $destino)
{
    // 1. Pega informações da imagem
    list($largura, $altura, $tipo) = getimagesize($file['tmp_name']);

    // 2. Cria uma nova imagem na memória baseada no tipo original
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagem = imagecreatefromjpeg($file['tmp_name']);
            break;
        case IMAGETYPE_PNG:
            $imagem = imagecreatefrompng($file['tmp_name']);
            break;
        case IMAGETYPE_GIF:
            $imagem = imagecreatefromgif($file['tmp_name']);
            break;
        case IMAGETYPE_WEBP:
            $imagem = imagecreatefromwebp($file['tmp_name']);
            break;
        default:
            return false;
    }

    // 3. Redimensionar se for muito grande (Max 1200px de largura)
    $maxLargura = 1200;
    if ($largura > $maxLargura) {
        $novaAltura = ($altura / $largura) * $maxLargura;
        $novaImagem = imagecreatetruecolor($maxLargura, $novaAltura);

        // Mantém transparência se for PNG/WEBP
        imagealphablending($novaImagem, false);
        imagesavealpha($novaImagem, true);

        imagecopyresampled($novaImagem, $imagem, 0, 0, 0, 0, $maxLargura, $novaAltura, $largura, $altura);
        $imagem = $novaImagem;
    }

    // 4. Salvar como WEBP (Qualidade 80 - Leve e Bonito)
    // O destino deve terminar com .webp
    $sucesso = imagewebp($imagem, $destino, 80);

    // Limpa a memória
    imagedestroy($imagem);

    return $sucesso;
}

$msg = "";
$editData = null;

// CARREGAR DADOS
if (isset($_GET['editar'])) {
    $data = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true);
    foreach ($data as $item) {
        if ($item['id'] == $_GET['editar']) {
            $editData = $item;
            break;
        }
    }
}

// SALVAR
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_exists($jsonFile) ? file_get_contents($jsonFile) : '[]', true) ?? [];

    // ID Correto
    $id = !empty($_POST['id_editar']) ? $_POST['id_editar'] : time();

    // Tratamento de datas
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
    $legacyDate = $data_inicio ?: ($_POST['data_antiga'] ?? date('d/m/Y'));

    // --- UPLOAD OTIMIZADO ---
    $imgPath = $_POST['imagem_atual'] ?? '';

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        // Forçamos a extensão .webp
        $nomeArquivo = time() . ".webp";
        $caminhoRelativo = "img/agenda/" . $nomeArquivo;
        $caminhoCompleto = "../" . $caminhoRelativo;

        if (uploadOtimizado($_FILES['imagem'], $caminhoCompleto)) {
            $imgPath = $caminhoRelativo;
            // Apaga a antiga
            if (!empty($_POST['imagem_atual']) && file_exists("../" . $_POST['imagem_atual'])) {
                @unlink("../" . $_POST['imagem_atual']);
            }
        } else {
            $msg = "<p class='error'>Erro ao processar imagem. Tente JPG ou PNG.</p>";
        }
    }

    $newItem = [
        "id" => $id,
        "img" => $imgPath,
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
    foreach ($data as $k => $v) {
        if ($v['id'] == $id) {
            $data[$k] = $newItem;
            $updated = true;
            break;
        }
    }
    if (!$updated) array_unshift($data, $newItem);

    if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT))) {
        $msg = "<p class='success'>✅ Evento salvo e Imagem Otimizada!</p>";
        $editData = null;
    } else {
        $msg = "<p class='error'>Erro ao salvar JSON.</p>";
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

// PREPARA CAMPOS
$val_data_inicio = "";
$val_data_fim = "";
if ($editData) {
    if (!empty($editData['data_inicio'])) $val_data_inicio = DateTime::createFromFormat('d/m/Y', $editData['data_inicio'])->format('Y-m-d');
    elseif (!empty($editData['data'])) $val_data_inicio = DateTime::createFromFormat('d/m/Y', $editData['data'])->format('Y-m-d');

    if (!empty($editData['data_fim'])) $val_data_fim = DateTime::createFromFormat('d/m/Y', $editData['data_fim'])->format('Y-m-d');
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
        /* ESTILO MANTIDO */
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

        button {
            background: #27ae60;
            color: white;
            font-weight: 600;
            cursor: pointer;
            border: none;
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

        .btn-edit {
            background: #f39c12;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
        }

        .btn-del {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
        }

        .success {
            color: #27ae60;
            background: #e8f5e9;
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

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_editar" value="<?= $editData['id'] ?? '' ?>">
            <input type="hidden" name="imagem_atual" value="<?= $editData['img'] ?? '' ?>">
            <input type="hidden" name="data_antiga" value="<?= $editData['data'] ?? '' ?>">

            <label>Título:</label> <input type="text" name="titulo" value="<?= $editData['titulo'] ?? '' ?>" required>

            <div class="grid-dates">
                <div>
                    <label style="color:#27ae60">Início</label>
                    <div class="date-group">
                        <div><small>Data</small><input type="date" name="data_inicio" value="<?= $val_data_inicio ?>" required></div>
                        <div><small>Hora</small><input type="time" name="hora_inicio" value="<?= $editData['hora_inicio'] ?? '' ?>"></div>
                    </div>
                </div>
                <div>
                    <label style="color:#c0392b">Fim</label>
                    <div class="date-group">
                        <div><small>Data</small><input type="date" name="data_fim" value="<?= $val_data_fim ?>"></div>
                        <div><small>Hora</small><input type="time" name="hora_fim" value="<?= $editData['hora_fim'] ?? '' ?>"></div>
                    </div>
                </div>
            </div>

            <label>Local:</label> <input type="text" name="local" value="<?= $editData['local'] ?? '' ?>" required>
            <label>Descrição:</label> <textarea name="texto" required><?= $editData['texto'] ?? '' ?></textarea>

            <label>Imagem (Otimização Automática para WebP ⚡):</label>
            <input type="file" name="imagem" accept="image/*">

            <button type="submit">SALVAR</button>
            <?php if ($editData): ?> <a href="painel_agenda.php" style="display:block; text-align:center; margin-top:10px; color:#666;">Cancelar</a> <?php endif; ?>
        </form>

        <div style="margin-top:40px;">
            <h3>Eventos</h3>
            <?php if ($list): foreach ($list as $i): ?>
                    <div class="item">
                        <div class="item-info">
                            <?php if (!empty($i['img'])): ?> <img src="../<?= $i['img'] ?>" width="60" height="60" style="object-fit:cover; border-radius:4px;"> <?php endif; ?>
                            <div>
                                <strong><?= $i['titulo'] ?></strong><br>
                                <small><?= $i['data_inicio'] ?? $i['data'] ?> <?= !empty($i['hora_inicio']) ? '• ' . $i['hora_inicio'] : '' ?></small>
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