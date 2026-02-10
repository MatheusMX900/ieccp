<?php
// --- 1. CONFIGURAÇÕES E INCLUDES ---
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// --- 2. VERIFICAÇÃO DE LIVE (Cache) ---
$dadosLive = ['is_live' => false];
$caminhoCacheLive = 'scripts/live_status.json';
if (file_exists($caminhoCacheLive)) {
  $conteudo = @file_get_contents($caminhoCacheLive);
  if ($conteudo) {
    $dadosLive = json_decode($conteudo, true);
  }
}
$temLive = !empty($dadosLive['is_live']) && $dadosLive['is_live'] === true;

// --- 3. CARREGAMENTO DE DADOS ---

// A. Presente Diário
$pdHoje = null;
if (isset($pdo)) {
  try {
    $stmt = $pdo->query("SELECT * FROM presente_diario ORDER BY data_publicacao DESC LIMIT 1");
    $pdHoje = $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (Exception $e) {
  }
}

// B. JSONs
$agenda = array_slice(lerJson('agenda.json'), 0, 6);
$pastoral = array_slice(lerJson('pastoral.json'), 0, 4);
$noticias = array_slice(lerJson('noticias.json'), 0, 4);
$missionarios = array_slice(lerJson('missionarios.json'), 0, 4);
?>

<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Portal IECCP</title>

  <meta name="description" content="IECCP - Uma família de fé em Cachoeira Paulista.">
  <meta name="keywords" content="igreja, evangelho, IECCP, cachoeira paulista">
  <meta name="author" content="Matheus Andrade, Luiz Charleaux">

  <meta property="og:title" content="Portal IECCP">
  <meta property="og:description" content="Uma família de fé em Cachoeira Paulista.">
  <meta property="og:image" content="https://ieccp.com.br/img/logo.png">

  <link rel="icon" type="image/png" href="/img/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/img/favicon.svg" />
  <link rel="shortcut icon" href="/img/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="/img/apple-touch-icon.png" />
  <meta name="apple-mobile-web-app-title" content="Portal IECCP" />
  <link rel="manifest" href="/img/site.webmanifest" />

  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Church",
      "name": "Igreja Evangélica Congregacional de Cachoeira Paulista",
      "url": "https://ieccp.com.br",
      "logo": "https://ieccp.com.br/img/logo.png",
      "image": "https://ieccp.com.br/img/logo.png",
      "description": "Uma família de fé em Cachoeira Paulista.",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "R. Conselheiro Rodrigues Alves, 358",
        "addressLocality": "Cachoeira Paulista",
        "addressRegion": "SP",
        "postalCode": "12630-000",
        "addressCountry": "BR"
      },
      "sameAs": [
        "https://www.facebook.com/ieccp",
        "https://www.instagram.com/ieccp",
        "https://www.youtube.com/@ieccp"
      ]
    }
  </script>
  <meta name="theme-color" content="#002B5B" />

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <link rel="stylesheet" href="styles/global.css" />
  <link rel="stylesheet" href="styles/header.css" />
  <link rel="stylesheet" href="styles/gallery.css" />
  <link rel="stylesheet" href="styles/agenda.css" />
  <link rel="stylesheet" href="styles/home.css" />

  <link rel="manifest" href="/manifest.json" />

  <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
  <script>
    window.OneSignalDeferred = window.OneSignalDeferred || [];
    OneSignalDeferred.push(async function(OneSignal) {
      await OneSignal.init({
        appId: "574229ff-3df7-474b-8e1c-4d6d3bca5ade",
        safari_web_id: "web.onesignal.auto.31f2bfbe-48d0-4a72-b7e0-d44022a2d3bb",
        notifyButton: {
          enable: false
        },
      });
    });
    if ("serviceWorker" in navigator) {
      window.addEventListener("load", () => {
        navigator.serviceWorker.register("/sw.js").catch((err) => console.log("Erro SW:", err));
      });
    }
  </script>
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
        <li><a href="presente-diario">Presente Diário</a></li>
        <li><a href="noticias">Notícias</a></li>
        <li><a href="missoes">Missões</a></li>
        <li><button id="btn-tema" title="Mudar Tema"><i class="fa-solid fa-sun"></i></button></li>
      </ul>
    </nav>
  </header>

  <section class="hero-section">
    <div class="wrapper">
      <div class="hero-grid <?php echo $temLive ? 'mode-live' : ''; ?>">

        <?php if ($temLive): ?>
          <div class="master-card card-live-big">
            <div class="live-header-big">
              <div class="live-status"><span class="pulse-dot"></span> AO VIVO AGORA</div>
              <h2 class="live-title-big"><?php echo htmlspecialchars($dadosLive['titulo']); ?></h2>
            </div>
            <div class="video-container-big">
              <iframe
                src="https://www.youtube.com/embed/<?php echo $dadosLive['video_id']; ?>?autoplay=1&mute=0"
                title="Culto Ao Vivo"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
              </iframe>
            </div>
            <div class="live-footer-big">
              <p>Você está assistindo ao culto online da IECCP.</p>
              <a href="https://youtube.com/watch?v=<?php echo $dadosLive['video_id']; ?>" target="_blank" class="btn-youtube-big">
                <i class="fa-brands fa-youtube"></i> Abrir Chat
              </a>
            </div>
          </div>

        <?php else: ?>

          <div class="master-card card-welcome">
            <span class="welcome-badge">Seja Bem-vindo</span>
            <h1 class="welcome-title">Glorificar, Edificar <br />& Proclamar</h1>
            <p class="welcome-text">Uma comunidade viva em Cachoeira Paulista, pronta para te receber de braços abertos.</p>
          </div>

          <div class="master-card card-times">
            <div class="times-header">
              <h2>Nossos Horários</h2><i class="fa-regular fa-clock" style="font-size: 1.5rem"></i>
            </div>
            <div class="time-row"><i class="fa-solid fa-bible"></i> <span>Dom 09h — EBD</span></div>
            <div class="time-row"><i class="fa-solid fa-bible"></i> <span>Dom 10h20 — Culto da Manhã</span></div>
            <div class="time-row"><i class="fa-solid fa-church"></i> <span>Dom 18h30 — Culto da Noite</span></div>
            <div class="time-row"><i class="fa-solid fa-hands-praying"></i> <span>Qua 19h30 — Reunião de Oração</span></div>
          </div>

          <div class="master-card card-verse">
            <i class="fa-solid fa-quote-left quote-icon" style="font-size: 3rem; color: rgba(255, 204, 0, 0.4)"></i>
            <p class="verse-text">"Alegrei-me quando me disseram: vamos à casa do Senhor"</p>
            <span class="verse-ref" style="color: var(--secondary); font-weight: 800">SALMOS 122:1</span>
          </div>

          <?php if ($pdHoje):
            // --- ATUALIZAÇÃO: LINK AMIGÁVEL DO PD ---
            $slugPD = gerarSlug($pdHoje['titulo']);
            $linkPD = "/pd/" . $slugPD;

            $bgStyle = !empty($pdHoje['imagem'])
              ? "background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.9)), url('{$pdHoje['imagem']}') center/cover;"
              : "background: linear-gradient(135deg, var(--primary), #001f3f);";
          ?>
            <a href="<?php echo $linkPD; ?>" class="master-card card-map" style="<?php echo $bgStyle; ?> text-decoration: none; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; color: white;">
              <span style="background: var(--secondary); color: #000; padding: 3px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: bold; text-transform: uppercase; margin-bottom: 8px;">Presente Diário</span>
              <h3 style="font-family: 'Oswald', sans-serif; font-size: 1.2rem; margin: 0; line-height: 1.2;">
                <?php echo htmlspecialchars($pdHoje['titulo']); ?>
              </h3>
              <span style="font-size: 0.8rem; margin-top: 10px; color: var(--secondary); font-weight: 500;">
                Ler Mensagem <i class="fa-solid fa-arrow-right" style="font-size: 0.7em;"></i>
              </span>
            </a>

          <?php else: ?>

            <a href="https://maps.app.goo.gl/pz23waNTkSb8d4Ru7" target="_blank" class="master-card card-map">
              <div class="map-overlay">
                <i class="fa-solid fa-location-dot" style="font-size: 2rem; margin-bottom: 10px; color: var(--secondary);"></i>
                <h3 style="font-family: 'Oswald', sans-serif; margin: 0">ONDE ESTAMOS</h3>
                <span class="btn-map">Ver Rotas</span>
              </div>
            </a>

          <?php endif; ?>

        <?php endif; ?>
      </div>
    </div>
  </section>

  <div class="background-fixo-louvor">
    <div class="pelicula-escura">
      <main>

        <section id="agenda">
          <div class="section-header">
            <h2 class="section-title">Próximos Eventos</h2>
            <a href="agenda.html" class="section-link"><span>Ver Agenda Completa</span><i class="fa-solid fa-arrow-right-long"></i></a>
          </div>
          <div class="carousel-container">
            <?php if (empty($agenda)): ?>
              <p style="color:#fff; text-align:center; width:100%; padding: 20px;">Nenhum evento agendado no momento.</p>
              <?php else: foreach ($agenda as $ev): ?>
                <div class="gallery-item">
                  <div class="card-link-wrapper" style="cursor: default">
                    <div class="card-image-box">
                      <img src="<?php echo formatarImagem($ev['img'] ?? ''); ?>" alt="<?php echo $ev['titulo']; ?>" loading="lazy" />
                      <span class="date-badge"><?php echo formatarDataAgenda($ev); ?></span>
                    </div>
                    <div class="card-content">
                      <h3 class="desc"><?php echo $ev['titulo']; ?></h3>
                      <div class="event-location" style="margin-top: auto; padding-top: 10px; border-top: 1px solid var(--card-border); font-weight: bold; font-size: 0.85rem; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-location-dot"></i>
                        <span class="texto-local"><?php echo $ev['local'] ?? 'IECCP'; ?></span>
                      </div>
                    </div>
                  </div>
                </div>
            <?php endforeach;
            endif; ?>
          </div>
        </section>

        <section id="pastoral">
          <div class="section-header">
            <h2 class="section-title">Palavra Pastoral</h2>
            <a href="pastoral.html" class="section-link"><span>Ler Mais Artigos</span><i class="fa-solid fa-arrow-right-long"></i></a>
          </div>
          <div class="carousel-container">
            <?php foreach ($pastoral as $item):
              // --- ATUALIZAÇÃO: LINK AMIGÁVEL PASTORAL ---
              $slug = gerarSlug($item['titulo']);
              $link = "/pastoral/" . $slug;
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

        <section id="noticias">
          <div class="section-header">
            <h2 class="section-title">Últimas Notícias</h2>
            <a href="noticias.html" class="section-link"><span>Ver Todas</span><i class="fa-solid fa-arrow-right-long"></i></a>
          </div>
          <div class="carousel-container">
            <?php foreach ($noticias as $item):
              // --- ATUALIZAÇÃO: LINK AMIGÁVEL NOTÍCIA ---
              $slug = gerarSlug($item['titulo']);
              $link = "/noticia/" . $slug;
            ?>
              <div class="gallery-item">
                <a href="<?php echo $link; ?>" class="card-link-wrapper">
                  <div class="card-image-box">
                    <img src="<?php echo formatarImagem($item['img'] ?? ''); ?>" alt="<?php echo $item['titulo']; ?>" loading="lazy" />
                  </div>
                  <div class="card-content">
                    <h3 class="desc"><?php echo $item['titulo']; ?></h3>
                    <span class="read-more">Ler Mais</span>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

        <section id="missionarios">
          <div class="section-header">
            <h2 class="section-title">Nossos Missionários</h2>
            <a href="/missoes" class="section-link"><span>Conhecer Projetos</span><i class="fa-solid fa-arrow-right-long"></i></a>
          </div>
          <div class="carousel-container">
            <?php foreach ($missionarios as $item):
              // Lógica de link igual a da página de missões
              $linkOriginal = $item['link'] ?? '#';
              $linkLimpo = str_replace('.html', '', $linkOriginal);
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
                    <p class="short-desc"><?php echo resumirTexto($item['descricao'] ?? '', 80); ?></p>
                    <span class="read-more">Apoiar</span>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        </section>

      </main>
    </div>
  </div>

  <a href="#" id="back-to-top" title="Voltar ao topo"><i class="fa-solid fa-arrow-up"></i></a>

  <footer>
    <div class="footer-content">
      <div class="footer-section brand">
        <h3>IECCP</h3>
        <p>Uma Igreja para adorar a Deus, edificar os salvos e proclamar o Evangelho.</p>
      </div>
      <div class="footer-section links">
        <h3>Navegação</h3>
        <ul>
          <li><a href="sobre">Nossa História</a></li>
          <li><a href="missoes">Missionários</a></li>
          <li><a href="sobre">Doutrinas</a></li>
          <li><a href="https://wa.me/+551231012589" target="_blank">Fale Conosco</a></li>
        </ul>
      </div>
      <div class="footer-section contact">
        <h3>Entre em contato</h3>
        <p><i class="fa-solid fa-location-dot"></i> R. Conselheiro Rodrigues Alves, 358</p>
        <p><i class="fa-solid fa-envelope"></i> congregacp.secretaria@gmail.com</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>Criado por Matheus Andrade e Luiz Charleaux © 2026 – IECCP</p>
    </div>
  </footer>

  <div id="modal-notificacao" class="modal-overlay">
    <div class="modal-box">
      <h3>Fique por dentro! ⛪</h3>
      <p>Receba avisos sobre cultos e eventos.</p>
      <div class="modal-botoes">
        <button id="btn-agora-nao" class="btn-secundario">Agora não</button>
        <button id="btn-aceitar" class="btn-primario">Sim!</button>
      </div>
    </div>
  </div>

  <div id="cookie-banner" class="cookie-banner">
    <p>🍪 Usamos cookies para melhorar sua experiência.</p>
    <button id="btn-cookie-ok">Entendi</button>
  </div>

  <div id="modal-install" class="modal-overlay" style="display: none;">
    <div class="modal-box">
      <h3>Instale o App da IECCP 📲</h3>
      <p>Acesse a agenda, cultos e notícias direto da sua tela inicial, sem ocupar espaço.</p>
      <div class="modal-botoes">
        <button id="btn-install-nao" class="btn-secundario">Agora não</button>
        <button id="btn-install-sim" class="btn-primario">Instalar</button>
      </div>
    </div>
  </div>

  <script src="scripts/tema.js" defer></script>
  <script src="scripts/menu.js" defer></script>
  <script src="scripts/popup.js"></script>

  <script>
    const hero = document.querySelector(".hero-section");
    let ticking = false;
    window.addEventListener("scroll", function() {
      if (!ticking) {
        window.requestAnimationFrame(function() {
          let scrollPosition = window.pageYOffset;
          hero.style.backgroundPosition = "center calc(50% + " + scrollPosition * 0.4 + "px)";
          ticking = false;
        });
        ticking = true;
      }
    });

    let deferredPrompt;
    const installModal = document.getElementById('modal-install');
    const btnInstallSim = document.getElementById('btn-install-sim');
    const btnInstallNao = document.getElementById('btn-install-nao');

    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
      if (!localStorage.getItem('pwa_interagiu')) {
        installModal.style.display = 'flex';
      }
    });

    btnInstallSim.addEventListener('click', async () => {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        const {
          outcome
        } = await deferredPrompt.userChoice;
        deferredPrompt = null;
      }
      installModal.style.display = 'none';
      localStorage.setItem('pwa_interagiu', 'true');
    });

    btnInstallNao.addEventListener('click', () => {
      installModal.style.display = 'none';
      localStorage.setItem('pwa_interagiu', 'true');
    });
  </script>
</body>

</html>