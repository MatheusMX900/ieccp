<?php
// --- LÓGICA PHP (Fica invisível no topo) ---
// Tenta ler o arquivo JSON gerado pelo script na pasta scripts/
$caminhoCache = 'scripts/live_status.json'; 
$dadosLive = ['is_live' => false];

if (file_exists($caminhoCache)) {
    $conteudo = @file_get_contents($caminhoCache);
    if ($conteudo) {
        $dadosLive = json_decode($conteudo, true);
    }
}
?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>IECCP | Igreja Evangélica Congregacional</title>
    
    <meta name="keywords" content="igreja, evangelho, IECCP, cachoeira paulista, cristo, fe, deus, espirito santo, amor, comunhao, identidade">
    <meta
      name="description"
      content="IECCP - Uma família de fé em Cachoeira Paulista. Participe dos nossos cultos."
    >
    <meta name="author" content="Matheus Andrade, Luiz Charleaux">
    <meta http-equiv="refresh" content="60">

    <link rel="icon" type="image/svg+xml" href="img/favicon2.png" />

    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
      rel="stylesheet"
    />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;700&family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="styles/global.css" />
    <link rel="stylesheet" href="styles/header.css" />
    <link rel="stylesheet" href="styles/gallery.css" />
    <link rel="stylesheet" href="styles/agenda.css" />
    <link rel="stylesheet" href="styles/home.css" />

    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#002B5B" />

    <script
      src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js"
      defer
    ></script>
    <script>
      window.OneSignalDeferred = window.OneSignalDeferred || [];
      OneSignalDeferred.push(async function (OneSignal) {
        await OneSignal.init({
          appId: "574229ff-3df7-474b-8e1c-4d6d3bca5ade",
          safari_web_id:
            "web.onesignal.auto.31f2bfbe-48d0-4a72-b7e0-d44022a2d3bb",
          notifyButton: { enable: false },
        });
      });
    </script>

    <script>
      if ("serviceWorker" in navigator) {
        window.addEventListener("load", () => {
          navigator.serviceWorker
            .register("/sw.js")
            .then((reg) => console.log("App pronto para instalar!", reg))
            .catch((err) => console.log("Erro ao registrar app:", err));
        });
      }
    </script>
  </head>

  <body>
    <header>
      <a href="#" class="branding">
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
          <li><a href="noticias">Notícias</a></li>
          <li><a href="missoes">Missões</a></li>
          <li><a href="sobre">Sobre</a></li>
          <li>
            <button id="btn-tema" title="Mudar Tema">
              <i class="fa-solid fa-sun"></i>
            </button>
          </li>
        </ul>
      </nav>
    </header>

    <section class="hero-section">
      <div class="wrapper">
        <div class="hero-grid">
          
          <div class="master-card card-welcome">
            <span class="welcome-badge">Seja Bem-vindo</span>
            <h1 class="welcome-title">
              Glorificar, Edificar <br />& Proclamar
            </h1>
            <p class="welcome-text">
              Uma comunidade viva em Cachoeira Paulista, pronta para te receber
              de braços abertos.
            </p>
          </div>

          <?php if (!empty($dadosLive['is_live']) && $dadosLive['is_live'] === true): ?>
            
            <div class="master-card card-live">
                <div class="live-header">
                    <div class="live-badge">
                        <span class="pulse-dot"></span> AO VIVO
                    </div>
                    <i class="fa-brands fa-youtube icon-live"></i>
                </div>
                
                <h3 class="live-title">
                    <?php echo htmlspecialchars($dadosLive['titulo']); ?>
                </h3>

                <div class="video-responsive">
                    <iframe 
                        src="https://www.youtube.com/embed/<?php echo $dadosLive['video_id']; ?>?autoplay=1&mute=1" 
                        title="Culto Ao Vivo" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
                
                <a href="https://youtube.com/watch?v=<?php echo $dadosLive['video_id']; ?>" target="_blank" class="btn-assistir">
                   Assistir no YouTube
                </a>
            </div>

          <?php else: ?>

            <div class="master-card card-times">
              <div class="times-header">
                <h2>Nossos Horários</h2>
                <i class="fa-regular fa-clock" style="font-size: 1.5rem"></i>
              </div>
              <div class="time-row">
                <i class="fa-solid fa-bible"></i> <span>Dom 09h — EBD</span>
              </div>
              <div class="time-row">
                <i class="fa-solid fa-bible"></i>
                <span>Dom 10h20 — Culto da Manhã</span>
              </div>
              <div class="time-row">
                <i class="fa-solid fa-church"></i>
                <span>Dom 18h30 — Culto da Noite</span>
              </div>
              <div class="time-row">
                <i class="fa-solid fa-hands-praying"></i>
                <span>Qua 7h30 — Reunião de Oração</span>
              </div>
            </div>

          <?php endif; ?>
          <div class="master-card card-verse">
            <i
              class="fa-solid fa-quote-left quote-icon"
              style="font-size: 3rem; color: rgba(255, 204, 0, 0.4)"
            ></i>
            <p class="verse-text">
              "Alegrei-me quando me disseram: vamos à casa do Senhor"
            </p>
            <span
              class="verse-ref"
              style="color: var(--secondary); font-weight: 800"
              >SALMOS 122:1</span
            >
          </div>

          <a
            href="https://maps.app.goo.gl/pz23waNTkSb8d4Ru7"
            target="_blank"
            class="master-card card-map"
          >
            <div class="map-overlay">
              <i
                class="fa-solid fa-location-dot"
                style="
                  font-size: 2rem;
                  margin-bottom: 10px;
                  color: var(--secondary);
                "
              ></i>
              <h3
                style="font-family: 'Oswald', sans-serif; margin: 0"
              >
                ONDE ESTAMOS
              </h3>
              <span class="btn-map">Ver Rotas</span>
            </div>
          </a>
        </div>
      </div>
    </section>

    <div class="background-fixo-louvor">
      <div class="pelicula-escura">
        <main>
          <section id="agenda">
            <div class="section-header">
              <h2 class="section-title">Próximos Eventos</h2>
              <a href="agenda" class="section-link">
                <span>Ver Agenda Completa</span>
                <i class="fa-solid fa-arrow-right-long"></i>
              </a>
            </div>
            <div class="carousel-container" id="container-agenda"></div>
          </section>

          <section id="pastoral">
            <div class="section-header">
              <h2 class="section-title">Palavra Pastoral</h2>
              <a href="pastoral" class="section-link">
                <span>Ler Mais Artigos</span>
                <i class="fa-solid fa-arrow-right-long"></i>
              </a>
            </div>
            <div class="carousel-container" id="container-pastoral"></div>
          </section>

          <section id="noticias">
            <div class="section-header">
              <h2 class="section-title">Últimas Notícias</h2>
              <a href="noticias" class="section-link">
                <span>Ver Todas</span>
                <i class="fa-solid fa-arrow-right-long"></i>
              </a>
            </div>
            <div class="carousel-container" id="container-noticias"></div>
          </section>

          <section id="missionarios">
            <div class="section-header">
              <h2 class="section-title">Nossos Missionários</h2>
              <a href="missoes" class="section-link">
                <span>Conhecer Projetos</span>
                <i class="fa-solid fa-arrow-right-long"></i>
              </a>
            </div>
            <div class="carousel-container" id="container-missionarios"></div>
          </section>
        </main>
      </div>
    </div>

    <template id="template-padrao">
      <div class="gallery-item">
        <a href="" class="card-link-wrapper">
          <div class="card-image-box">
            <img src="" alt="Notícia" loading="lazy" />
          </div>
          <div class="card-content">
            <h3 class="desc">Título</h3>
            <span class="read-more">Ler Mais</span>
          </div>
        </a>
      </div>
    </template>

    <template id="template-missionarios">
      <div class="gallery-item">
        <a href="" class="card-link-wrapper">
          <div class="card-image-box">
            <img src="" alt="Missões" loading="lazy" />
          </div>
          <div class="card-content">
            <h3 class="desc">Nome</h3>
            <p class="short-desc">Descrição...</p>
            <span class="read-more">Apoiar</span>
          </div>
        </a>
      </div>
    </template>

    <template id="template-agenda">
      <div class="gallery-item">
        <div class="card-link-wrapper" style="cursor: default">
          <div class="card-image-box">
            <img src="" alt="Evento" loading="lazy" />

            <span class="date-badge">DATA</span>

            <span class="time-badge" style="display: none"></span>
          </div>
          <div class="card-content">
            <h3 class="desc">Título</h3>
            <p class="short-desc" style="margin-bottom: 15px"></p>

            <div
              class="event-location"
              style="
                margin-top: auto;
                padding-top: 10px;
                border-top: 1px solid var(--card-border);
                font-weight: bold;
                font-size: 0.85rem;
                display: flex;
                align-items: center;
                gap: 8px;
              "
            >
              <i class="fa-solid fa-location-dot"></i>
              <span class="texto-local">Local</span>
            </div>
          </div>
        </div>
      </div>
    </template>

    <template id="template-pastoral">
      <div class="gallery-item">
        <a href="" class="card-link-wrapper">
          <div class="card-image-box">
            <img src="" alt="" loading="lazy" />
          </div>
          <div class="card-content">
            <h3 class="desc">Título</h3>
            <p class="short-desc"></p>
            <span class="read-more">Ler Reflexão</span>
          </div>
        </a>
      </div>
    </template>

    <a href="#" id="back-to-top" title="Voltar ao topo">
      <i class="fa-solid fa-arrow-up"></i>
    </a>

    <footer>
      <div class="footer-content">
        <div class="footer-section brand">
          <h3>IECCP</h3>
          <p>
            Uma Igreja para adorar a Deus, edificar os salvos e proclamar o
            Evangelho.
          </p>
        </div>

        <div class="footer-section links">
          <h3>Navegação</h3>
          <ul>
            <li><a href="sobre">Nossa História</a></li>
            <li><a href="missoes">Missionários</a></li>
            <li><a href="sobre">Doutrinas</a></li>
            <li>
              <a href="https://wa.me/+551231012589" target="_blank"
                >Fale Conosco</a
              >
            </li>
          </ul>
        </div>

        <div class="footer-section contact">
          <h3>Entre em contato</h3>
          <p>
            <i class="fa-solid fa-location-dot"></i>
            <a href="https://maps.app.goo.gl/pz23waNTkSb8d4Ru7">
              Av. Conselheiro Rodrigues Alves, 358, Cachoeira Paulista - SP,
              12630-041</a
            >
          </p>
          <p>
            <i class="fa-solid fa-envelope"></i>
            <a href="mailto:congregacp.secretaria@gmail.com"
              >congregacp.secretaria@gmail.com</a
            >
          </p>
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

    <script>
      // Efeito Parallax no Hero
      const hero = document.querySelector(".hero-section");
      let ticking = false;

      window.addEventListener("scroll", function () {
        if (!ticking) {
          window.requestAnimationFrame(function () {
            let scrollPosition = window.pageYOffset;
            hero.style.backgroundPosition =
              "center calc(50% + " + scrollPosition * 0.4 + "px)";
            ticking = false;
          });
          ticking = true;
        }
      });
    </script>

    <script src="scripts/tema.js" defer></script>
    <script src="scripts/menu.js" defer></script>
    <script src="scripts/conteudo.js" defer></script>
    <script src="scripts/popup.js"></script>
  </body>
</html>