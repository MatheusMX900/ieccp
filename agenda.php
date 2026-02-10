<?php
require_once __DIR__ . '/includes/functions.php';
$agenda = lerJson('agenda.json');
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Agenda IECCP" />
  <title>IECCP - Agenda</title>

  <link rel="icon" type="image/svg+xml" href="img/favicon2.png" />
  <link rel="stylesheet" href="styles/global.css" />
  <link rel="stylesheet" href="styles/header.css" />
  <link rel="stylesheet" href="styles/gallery.css" />
  <link rel="stylesheet" href="styles/pages.css" />
  <link rel="stylesheet" href="styles/agenda.css" />

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
        <li><a href="agenda" class="active">Agenda</a></li>
        <li><a href="pastoral">Pastoral</a></li>
        <li><a href="presente-diario">Presente Diário</a></li>
        <li><a href="noticias">Notícias</a></li>
        <li><a href="missoes">Missões</a></li>
        <li><button id="btn-tema"><i class="fa-solid fa-sun"></i></button></li>
      </ul>
    </nav>
  </header>

  <section class="news-page">
    <div class="page-header" style="text-align: center; margin-bottom: 50px; padding-bottom: 20px; border-bottom: 2px solid var(--card-border);">
      <h1 style="font-family: 'Oswald'; font-size: 2.5rem; text-transform: uppercase;">Agenda e Eventos</h1>
    </div>

    <div class="news-grid" id="container-agenda">
      <?php if (empty($agenda)): ?>
        <p style="text-align:center; width:100%;">Nenhum evento agendado.</p>
        <?php else: foreach ($agenda as $ev):
          // Link amigável de evento
          $link = "/evento/" . gerarSlug($ev['titulo']);
        ?>
          <div class="gallery-item">
            <a href="<?php echo $link; ?>" class="card-link-wrapper" style="text-decoration:none; color:inherit;">
              <div class="card-image-box">
                <img src="<?php echo formatarImagem($ev['img'] ?? ''); ?>" alt="<?php echo $ev['titulo']; ?>" loading="lazy" />
                <span class="date-badge"><?php echo formatarDataAgenda($ev); ?></span>
              </div>
              <div class="card-content">
                <h3 class="desc"><?php echo $ev['titulo']; ?></h3>
                <div class="event-location" style="border-top: 1px solid #eee; padding-top: 10px; font-size: 0.85rem; color: #27ae60; font-weight: bold;">
                  <i class="fa-solid fa-location-dot"></i>
                  <span class="texto-local"><?php echo $ev['local'] ?? 'IECCP'; ?></span>
                </div>
              </div>
            </a>
          </div>
      <?php endforeach;
      endif; ?>
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