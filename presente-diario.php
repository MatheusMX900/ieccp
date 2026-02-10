<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php'; // Usa a função gerarSlug centralizada

$page_title = "Presente Diário - IECCP";
?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>

    <link rel="icon" type="image/svg+xml" href="img/favicon2.png" />
    <link rel="stylesheet" href="styles/global.css" />
    <link rel="stylesheet" href="styles/header.css" />
    <link rel="stylesheet" href="styles/gallery.css" />
    <link rel="stylesheet" href="styles/pages.css" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        .card-date {
            display: block;
            font-size: 0.85rem;
            color: #005fcc;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <header>
        <a href="/" class="branding">
            <img src="img/logo.png" alt="IECCP" />
            IECCP
        </a>
        <nav>
            <button id="btn-mobile" onclick="toggleMenu()">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </button>
            <ul id="menu" role="menu">
                <li><a href="contribua">Dizimos e Ofertas</a></li>
                <li><a href="agenda">Agenda</a></li>
                <li><a href="pastoral">Pastoral</a></li>
                <li><a href="presente-diario" class="active">Presente Diário</a></li>
                <li><a href="noticias">Notícias</a></li>
                <li><a href="missoes">Missões</a></li>
                <li><button id="btn-tema" title="Mudar Tema"><i class="fa-solid fa-sun"></i></button></li>
            </ul>
        </nav>
    </header>

    <section class="page-content" style="text-align: center; padding: 40px 20px; padding-top: 100px">
        <h1 class="content_title">Presente Diário</h1>
        <p style="max-width: 600px; margin: 0 auto 40px; color: var(--text-color);">
            Edificação diária para sua vida espiritual. Mensagens da RTM Brasil.
        </p>

        <div class="news-grid">
            <?php
            try {
                $stmt = $pdo->query("SELECT * FROM presente_diario ORDER BY data_publicacao DESC LIMIT 50");
                $devocionais = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($devocionais) {
                    foreach ($devocionais as $pd) {
                        $resumoRaw = strip_tags($pd['frase_destaque']);
                        $resumo = mb_strlen($resumoRaw) > 120 ? mb_substr($resumoRaw, 0, 117) . "..." : $resumoRaw;
                        $titulo = htmlspecialchars($pd['titulo']);

                        // --- AQUI ESTÁ A CORREÇÃO DO LINK ---
                        $slug = gerarSlug($pd['titulo']);
                        $link = "/pd/" . $slug;

                        $img = !empty($pd['imagem']) ? $pd['imagem'] : "img/capa-padrao-pd.jpg";
                        $data = date('d/m/Y', strtotime($pd['data_publicacao']));
            ?>
                        <div class="gallery-item">
                            <a href="<?php echo $link; ?>" class="card-link-wrapper" aria-label="Ler: <?php echo $titulo; ?>">
                                <div class="card-image-box">
                                    <img src="<?php echo $img; ?>" alt="<?php echo $titulo; ?>" loading="lazy" style="object-fit: cover;" onerror="this.onerror=null;this.src='img/capa-padrao-pd.jpg';" />
                                </div>
                                <div class="card-content">
                                    <h3 class="desc"><?php echo $titulo; ?></h3>
                                    <span class="card-date"><i class="fa-regular fa-calendar"></i> <?php echo $data; ?></span>
                                    <p class="short-desc"><?php echo $resumo; ?></p>
                                </div>
                            </a>
                        </div>
            <?php
                    }
                } else {
                    echo "<p>Nenhum devocional encontrado.</p>";
                }
            } catch (PDOException $e) {
                echo "<p>Erro ao carregar devocionais.</p>";
            }
            ?>
        </div>
    </section>

    <footer>
        <div class="footer-bottom">
            <p>Criado por Matheus Andrade e Luiz Charleaux © 2026 – IECCP</p>
        </div>
    </footer>

    <script src="scripts/tema.js"></script>
    <script src="scripts/menu.js"></script>
</body>

</html>