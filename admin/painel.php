<?php
    session_start();

    if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
        header('Location: index.php');
        exit;
    }

    $mensagem = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagem'])) {
        
        // Definindo o caminho dos arquivos
        $arquivoJson = '../data/noticias.json';
        $pastaImagens = '../img/';

        // Upload da imagem
        $nomeImagem = basename($_FILES['imagem']['name']);
        $caminhoFinalServidor = $pastaImagens . $nomeImagem;
        $caminhoJson = "img/" . $nomeImagem;

        // Confere se a imagem é real
        $check = getimagesize($_FILES['imagem']['tmp_name']);

        if ($check !== false) {
            if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoFinalServidor)) {
                
                // ## Manipulação do JSON
                // Se o arquivo não existir, apenas cria o arquivo, se não ele carrega
                $conteudoAtual = file_exists($arquivoJson) ? file_get_contents($arquivoJson) : '[]';
                $arrayNoticias = json_decode($conteudoAtual, true);
                if (!is_array($arrayNoticias)) $arrayNoticias = []; // Garante que será um array

                $idUnico = time(); // Gera um ID único baseado no timestamp atual

                $novaNoticia = [
                    "id" => $idUnico,
                    "img" => $caminhoJson,
                    "titulo" => $_POST['titulo'],
                    "texto" => $_POST['texto'],
                    "data" => date('d/m/Y'),
                ];

                // Adiciona a nova notícia no início da Array
                array_unshift($arrayNoticias, $novaNoticia);

                // Salva tudo no arquivo carregado
                if (file_put_contents($arquivoJson, json_encode($arrayNoticias, JSON_PRETTY_PRINT))) {
                    $mensagem = "<div class='sucesso'>Notícia publicada com sucesso!</div>";
                    } else {
                    $mensagem = "<div class='erro'>Erro ao salvar no JSON. Verifique permissões</div>";                      
                }
            } else {
                $mensagem = "<div class='erro'>Falha ao mover a imagem para a pasta /img</div>";                      
            }
        } else {
            $mensagem = "<div class='erro'>O arquivo enviado não é uma imagem!</div>";                      

        }
    }
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - Adicionar Notícia</title>
    <link rel="stylesheet" href="../styles/global.css">
    <link rel="stylesheet" href="../styles/header.css">
<style>
        /* CSS Específico do Painel */
        body { padding-top: 20px; background-color: var(--bg-body); }
        .container-admin { max-width: 600px; margin: 50px auto; background: var(--bg-card); padding: 30px; border-radius: 10px; border: 1px solid var(--card-border); }
        h2 { font-family: 'Oswald', sans-serif; margin-bottom: 20px; color: var(--text-color); }
        
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], input[type="file"] {
            width: 100%; padding: 10px; margin-bottom: 20px;
            background: var(--bg-body); border: 1px solid var(--card-border); color: var(--text-color); border-radius: 5px;
        }
        
        button.btn-salvar {
            background-color: var(--secondary); color: var(--header-bg); border: none; padding: 12px 20px;
            font-size: 1rem; font-weight: bold; border-radius: 5px; cursor: pointer; width: 100%; transition: 0.3s;
        }
        button.btn-salvar:hover { opacity: 0.9; }
        
        .sucesso { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        .erro { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        
        .logout { float: right; color: #ff6b6b; font-size: 0.9rem; text-decoration: underline; }

        textarea { width: 100%; height: 150px; padding: 10px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #ccc; font-family: sans-serif; }
        .container { max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; }
        body { background-color: var(--bg-body); }
        input, button { width: 100%; margin-bottom: 15px; padding: 10px; }
    </style>
</head>
<body>
    <div class="container-admin">
        <a href="logout.php" class="logout">Sair</a>
        <h2>Adicionar Nova Notícia</h2>

        <?php echo $mensagem ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Título da Notícia:</label>
            <input type="text" name="titulo" placeholder="Insira o Título" required>

            <label>Texto da Notícia:</label>
            <textarea name="texto" required placeholder="Escreva aqui..."></textarea>

            <label>Imagem da Capa:</label>
            <input type="file" name="imagem" accept="image/*" required>

            <button type="submit" class="btn-salvar">PUBLICAR NOTÍCIA</button>
        </form>

        <br>
        <a href="../index.html" target="_blank" style="display:block; text-align:center; margin-top:10px;">Ver site ></a>
    </div>
</body>
</html>