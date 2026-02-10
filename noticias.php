<?php
require_once __DIR__ . '/includes/functions.php';
$noticias = lerJson('noticias.json');
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Notícias da IECCP." />
  <title>IECCP - Notícias</title>

  <link rel="icon" type="image/svg+xml" href="img/favicon2.png" />
  <link rel="stylesheet" href="styles/global.css" />
  <link rel="stylesheet" href="styles/header.css" />
  <link rel="stylesheet" href="styles/gallery.css" />
  <link rel="stylesheet" href="styles/pages.css" />
  <link rel="stylesheet" href="styles/noticia.css" />

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>

<body>
  <header>
    <a href="/" class="branding"><img src="img/logo.png" alt="IECCP" />IECCP</a>
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
        <li><a href="presente-diario">Presente Diário</a></li>
        <li><a href="noticias" class="active">Notícias</a></li>
        <li><a href="missoes">Missões</a></li>
        <li><button id="btn-tema"><i class="fa-solid fa-sun"></i></button></li>
      </ul>
    </nav>
  </header>

  <main>
    <div class="page-header">
      <h1>Notícias e Atividades</h1>
    </div>

    <div id="feed-container">
      <?php foreach ($noticias as $item):
        $link = "/noticia/" . gerarSlug($item['titulo']);
        $texto = $item['texto'] ?? $item['descricao'] ?? "";
        $resumo = mb_substr(strip_tags($texto), 0, 200) . "...";
      ?>
        <a class="noticia-link" href="<?php echo $link; ?>">
          <article class="noticia-item">
            <div class="conteudo-texto">
              <span class="data-badge"><?php echo $item['data']; ?></span>
              <h2><?php echo $item['titulo']; ?></h2>
              <div class="texto-dinamico"><?php echo $resumo; ?></div>
            </div>
            <div class="conteudo-imagem">
              <img src="<?php echo formatarImagem($item['img'] ?? ''); ?>" alt="<?php echo $item['titulo']; ?>" loading="lazy" />
            </div>
          </article>
        </a>
      <?php endforeach; ?>
    </div>
  </main>

  <footer>
    <div class="footer-bottom">
      <p>© 2026 – IECCP</p>
    </div>
  </footer>
  <script src="scripts/tema.js"></script>
  <script src="scripts/menu.js"></script>
</body>

</html>