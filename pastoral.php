<?php
require_once __DIR__ . '/includes/functions.php';
$pastorais = lerJson('pastoral.json');
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>IECCP - Pastoral</title>

  <link rel="icon" type="image/svg+xml" href="img/favicon2.png" />
  <link rel="stylesheet" href="styles/global.css" />
  <link rel="stylesheet" href="styles/header.css" />
  <link rel="stylesheet" href="styles/gallery.css" />
  <link rel="stylesheet" href="styles/pages.css" />

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
        <li><a href="pastoral" class="active">Pastoral</a></li>
        <li><a href="presente-diario">Presente Diário</a></li>
        <li><a href="noticias">Notícias</a></li>
        <li><a href="missoes">Missões</a></li>
        <li><button id="btn-tema"><i class="fa-solid fa-sun"></i></button></li>
      </ul>
    </nav>
  </header>

  <section class="page-content" style="text-align: center; padding: 40px 20px; padding-top: 100px">
    <h1 class="content_title">Artigos Pastorais</h1>
    <div class="news-grid">
      <?php foreach ($pastorais as $item):
        $link = "/pastoral/" . gerarSlug($item['titulo']);
      ?>
        <div class="gallery-item">
          <a href="<?php echo $link; ?>" class="card-link-wrapper">
            <div class="card-image-box">
              <img src="<?php echo formatarImagem($item['img'] ?? ''); ?>" alt="<?php echo $item['titulo']; ?>" loading="lazy" />
            </div>
            <div class="card-content">
              <h3 class="desc"><?php echo $item['titulo']; ?></h3>
              <span class="read-more">Ler Reflexão</span>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <footer>
    <div class="footer-bottom">
      <p>© 2026 – IECCP</p>
    </div>
  </footer>
  <script src="scripts/tema.js"></script>
  <script src="scripts/menu.js"></script>
</body>

</html>