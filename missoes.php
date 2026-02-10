<?php
require_once __DIR__ . '/includes/functions.php';

// CORREÇÃO 1: Removemos o "/data/" pois a função lerJson já inclui o caminho
$missionarios = lerJson('missionarios.json');
?>

<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Conheça os missionários apoiados pela IECCP." />
  <title>IECCP - Missionários</title>

  <link rel="icon" type="image/svg+xml" href="/img/favicon2.png" />
  <link rel="stylesheet" href="/styles/global.css" />
  <link rel="stylesheet" href="/styles/header.css" />
  <link rel="stylesheet" href="/styles/pages.css" />
  <link rel="stylesheet" href="/styles/gallery.css" />

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>

<body>
  <header>
    <a href="/" class="branding">
      <img src="/img/logo.png" alt="IECCP" />
      IECCP
    </a>
    <nav>
      <button id="btn-mobile" onclick="toggleMenu()">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
      </button>
      <ul id="menu" role="menu">
        <li><a href="/contribua">Dizimos e Ofertas</a></li>
        <li><a href="/agenda">Agenda</a></li>
        <li><a href="/pastoral">Pastoral</a></li>
        <li><a href="/presente-diario">Presente Diário</a></li>
        <li><a href="/noticias">Notícias</a></li>
        <li><a href="/missoes" class="active">Missões</a></li>
        <li><button id="btn-tema"><i class="fa-solid fa-sun"></i></button></li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="page-title" style="text-align: center; padding: 40px 20px; padding-top: 100px">
      <h1 class="content_title">Nossos Missionários</h1>
      <p>Conheça e ore pelos projetos que apoiamos ao redor do mundo.</p>
    </section>

    <div class="news-grid">
      <?php if (empty($missionarios)): ?>
        <p style="text-align: center; width: 100%; grid-column: 1/-1;">Nenhum missionário encontrado.</p>
      <?php else: ?>
        <?php foreach ($missionarios as $item):
          // CORREÇÃO 2: Limpeza do Link para ficar Amigável
          $linkOriginal = $item['link'] ?? '#';

          // Remove o .html (ex: /missionarios/aurino.html vira /missionarios/aurino)
          $linkLimpo = str_replace('.html', '', $linkOriginal);

          // Garante a barra no início
          if ($linkLimpo !== '#' && substr($linkLimpo, 0, 1) !== '/') {
            $linkLimpo = '/' . $linkLimpo;
          }
        ?>
          <div class="gallery-item">
            <a href="<?php echo $linkLimpo; ?>" class="card-link-wrapper">
              <div class="card-image-box">
                <img src="<?php echo formatarImagem($item['img'] ?? ''); ?>" alt="<?php echo $item['titulo'] ?? 'Missionário'; ?>" loading="lazy" />
              </div>

              <div class="card-content">
                <h3 class="desc"><?php echo $item['titulo'] ?? 'Missionário'; ?></h3>
                <p class="short-desc"><?php echo resumirTexto($item['descricao'] ?? '', 100); ?></p>
                <span class="read-more">
                  Conhecer Projeto <i class="fa-solid fa-arrow-right"></i>
                </span>
              </div>
            </a>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

  <footer>
    <div class="footer-content">
      <div class="footer-section brand">
        <h3>IECCP</h3>
        <p>Uma Igreja para adorar a Deus, proclamar o evangelho e edificar a igreja.</p>
      </div>
      <div class="footer-section links">
        <h3>Navegação</h3>
        <ul>
          <li><a href="/historia">Nossa História</a></li>
          <li><a href="/missionarios">Missionários</a></li>
          <li><a href="/doutrinas">Doutrinas</a></li>
          <li><a href="https://wa.me/+551231012589" target="_blank">Fale Conosco</a></li>
        </ul>
      </div>
      <div class="footer-section contact">
        <h3>Encontre-nos</h3>
        <p><i class="fa-solid fa-location-dot"></i> R. Conselheiro Rodrigues Alves, 358</p>
        <p><i class="fa-solid fa-envelope"></i> congregacp.secretaria@gmail.com</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>Criado por Matheus Andrade e Luiz Charleaux © 2026 – IECCP</p>
    </div>
  </footer>

  <script src="/scripts/tema.js"></script>
  <script src="/scripts/menu.js"></script>
</body>

</html>