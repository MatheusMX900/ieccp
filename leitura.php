<?php
$id = isset($_GET['id']) ? $_GET['id'] : null; // Captura o ID da notícia, caso não tiver, é null
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'noticia';

// Caso não encontre nada, ele define esses valores como padrão
$page_title = "Leitura - IECCP";
$page_desc = "Uma família de fé em Cachoeira Paulista. Participe dos nossos cultos.";
$page_img = "https://ieccp.com.br/img/logo.png";
$page_url = "https://ieccp.com.br/leitura";

if ($id) {
  $json = "data/noticias.json";
  if ($tipo == 'pastoral') $json = "data/pastoral.json";
  if ($tipo == 'agenda') $json = "data/agenda.json"; // Implementação futura

  if (file_exists($json)) {
    $conteudo = file_get_contents($json);
    $dados = json_decode($conteudo, true);

    foreach ($dados as $item) {
      if ($item['id'] == $id) {
        $page_title = $item['titulo'];
        $texto_full = $item['texto'] ?? $item['descricao'] ?? $item['conteudo'] ?? "";
        $page_desc = mb_substr(strip_tags($texto_full), 0, 150) . "...";

        // Se tiver alguma imagem
        if (!empty($item['img'])) {
          $img_limpa = str_replace("../", "", $item['img']);
          $page_img = "https://ieccp.com.br/" . $img_limpa; // O Whatsapp é chato com isso aff veyr
        }

        $page_url = "https://ieccp.com.br/leitura?id=$id";
        if ($tipo == 'noticia') $short_link = "https://ieccp.com.br/n/$id";
        if ($tipo == 'pastoral') $short_link = "https://ieccp.com.br/p/$id";
        if ($tipo == 'agenda') $short_link = "https://ieccp.com.br/a/$id"; // Implementação futura
        break;
      }
    }
  }
}
?>

<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="image/svg+xml" href="img/favicon2.png" />

  <title><?php echo $page_title; ?></title>

  <!-- METAS -->
  <meta property="og:type" content="article" />
  <meta property="og:title" content="<?php echo $page_title; ?>" />
  <meta property="og:description" content="<?php echo $page_desc; ?>" />
  <meta property="og:image" content="<?php echo $page_img; ?>" />
  <meta property="og:url" content="<?php echo $page_url; ?>" />
  <meta property="og:site_name" content="IECCP" />

  <!-- Nem sei quem vai compartilhar no Twitter, mas pq não? -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?php echo $page_title; ?>" />
  <meta name="twitter:description" content="<?php echo $page_desc; ?>" />
  <meta name="twitter:image" content="<?php echo $page_img; ?>" />

  <link rel="stylesheet" href="/styles/global.css" />
  <link rel="stylesheet" href="/styles/header.css" />
  <link rel="stylesheet" href="/styles/gallery.css" />
  <link rel="stylesheet" href="/styles/agenda.css" />
  <link rel="stylesheet" href="/styles/leitura.css" />

  <link
    href="https://fonts.googleapis.com/css?family=Oswald:400,500,700"
    rel="stylesheet" />
  <link
    href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"
    rel="stylesheet" />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    rel="stylesheet" />

  <style>
    /* CONTAINER EDITORIAL */
    .news-container {
      max-width: 720px;
      margin: 48px auto;
      padding: 0 20px;
    }

    /* TÍTULO */
    .news-title {
      font-size: clamp(1.8rem, 3vw, 2.4rem);
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 12px;
    }

    /* META */
    .news-meta {
      font-size: 0.9rem;
      color: var(--text-color);
      margin-bottom: 28px;
    }

    /* IMAGEM */
    figure {
      margin: 0 0 28px 0;
    }

    figure img {
      width: 100%;
      height: auto;
      display: block;
      border-radius: 6px;
    }

    figcaption {
      font-size: 0.85rem;
      color: #666;
      margin-top: 6px;
    }

    /* TEXTO */
    .news-content p {
      font-size: 1.05rem;
      margin-bottom: 1.3em;
    }

    /* ACESSIBILIDADE */
    a:focus-visible,
    button:focus-visible {
      outline: 3px solid #005fcc;
      outline-offset: 3px;
    }

    /* RESPONSIVO */
    @media (max-width: 600px) {
      .news-container {
        margin-top: 32px;
      }

      .news-title {
        font-size: 1.6rem;
      }
    }

    footer {
      padding: 20px 10px;
    }

    .footer-bottom {
      padding: 10px 0;
      font-size: 0.85rem;
    }

    .news-nav {
      margin-bottom: 12px;
    }

    .back-link {
      font-size: 0.9rem;
      color: #005fcc;
      text-decoration: none;
      font-weight: 500;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    .back-link:focus-visible {
      outline: 3px solid #005fcc;
      outline-offset: 3px;
      border-radius: 4px;
    }

    .btn-share {
      background: transparent;
      border: 1px solid var(--card-border);
      color: var(--text-color);
      padding: 5px 12px;
      border-radius: 20px;
      cursor: ponter;
      font-family: "Poppins", sans-serif;
      font-size: 0.85rem;
      margin-left: 15px;
      transition: all 0.3s ease;
      display: inline-flex;
      /* Para alinhar o ícone */
      align-items: center;
      gap: 5px;
    }

    .btn-share:hover {
      background-color: var(--secondary);
      border-color: var(--secondary);
      color: #1c1c1c;
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

  <main>
    <article class="news-container" aria-labelledby="titulo">
      <nav aria-label="Navegação da notícia" class="news-nav">
        <a
          href="noticias"
          class="back-link"
          aria-label="Voltar para a lista de notícias">
          ← Voltar para Notícias
        </a>
      </nav>
      <h1 id="titulo" class="news-title">Carregando…</h1>

      <div class="news-meta">
        <time id="data"></time>

        <button onclick="adicionarCompartilhamento()" class="btn-share" title="Compartilhar">
          <i class="fa-solid fa-share-nodes"></i> Compartilhar
        </button>
      </div>

      <figure id="figure" hidden>
        <img id="imagem" alt="" />
      </figure>

      <section
        id="conteudo"
        class="news-content"
        aria-label="Texto da notícia"></section>
    </article>
  </main>

  <footer>
    <div class="footer-content">
      <div class="footer-section brand">
        <h3>IECCP</h3>
        <p>
          Uma Igreja para adorar a Deus, proclamar o evangelho e edificar a
          igreja.
        </p>
      </div>

      <div class="footer-section links">
        <h3>Navegação</h3>
        <ul>
          <li><a href="historia">Nossa História</a></li>
          <li><a href="missionarios">Missionários</a></li>
          <li><a href="doutrinas">Doutrinas</a></li>
          <li>
            <a href="https://wa.me/+551231012589" target="_blank">Fale Conosco</a>
          </li>
        </ul>
      </div>

      <div class="footer-section contact">
        <h3>Encontre-nos</h3>
        <p>
          <i class="fa-solid fa-location-dot"></i>
          <a href="https://maps.app.goo.gl/pz23waNTkSb8d4Ru7">
            R. Conselheiro Rodrigues Alves, 358, Cachoeira Paulista - SP,
            12630-000</a>
        </p>
        <p>
          <i class="fa-solid fa-envelope"></i>
          <a href="mailto:congregacp.secretaria@gmail.com">congregacp.secretaria@gmail.com</a>
        </p>
      </div>
    </div>

    <div class="footer-bottom">
      <p>Criado por Matheus Andrade e Luiz Charleaux © 2026 – IECCP</p>
    </div>
  </footer>

  <script>
    async function carregarConteudo() {
      // AQUI ESTÁ A MÁGICA: O PHP entrega o ID e o TIPO direto para o JS
      // Assim funciona tanto no link curto (/n/123) quanto no longo (?id=123)
      const id = "<?php echo $id; ?>";
      const tipo = "<?php echo $tipo; ?>";

      if (!id) {
        document.getElementById("titulo").textContent = "Conteúdo não encontrado.";
        return;
      }

      // Define qual JSON carregar (agora com BARRA / no início para não errar a pasta)
      let arquivoJson = "/data/noticias.json";
      let voltarLink = "/noticias";
      let voltarTexto = "← Voltar para Notícias";

      if (tipo === "pastoral") {
        arquivoJson = "/data/pastoral.json";
        voltarLink = "/pastoral";
        voltarTexto = "← Voltar para Pastoral";
      } else if (tipo === "agenda") {
        arquivoJson = "/data/agenda.json";
        voltarLink = "/agenda";
        voltarTexto = "← Voltar para Agenda";
      }

      // Atualiza o botão de voltar
      const btnVoltar = document.querySelector(".back-link");
      if (btnVoltar) {
        btnVoltar.href = voltarLink;
        btnVoltar.textContent = voltarTexto;
      }

      try {
        // Busca os dados (agora o caminho do JSON é absoluto)
        const res = await fetch(`${arquivoJson}?v=${Date.now()}`);
        if (!res.ok) throw new Error("Erro ao carregar arquivo");

        const dados = await res.json();
        const item = dados.find((n) => n.id == id);

        if (!item) {
          document.getElementById("titulo").textContent = "Conteúdo removido ou não encontrado.";
          return;
        }

        // Preenche a tela normalmente
        document.getElementById("titulo").textContent = item.titulo;
        document.title = `${item.titulo} | IECCP`;
        document.getElementById("data").textContent = item.data || "";

        if (item.img) {
          const img = document.getElementById("imagem");
          // Remove pontos e barras extras para garantir o caminho absoluto
          const srcLimpo = item.img.replace("../", "").replace(/^\//, "");
          img.src = "/" + srcLimpo; // Força a raiz
          img.alt = item.titulo;
          document.getElementById("figure").hidden = false;
        }

        const conteudo = document.getElementById("conteudo");
        conteudo.innerHTML = "";
        const textoFull = item.texto || item.descricao || item.conteudo || "";

        textoFull.split("\n").forEach((linha) => {
          if (linha.trim()) {
            const p = document.createElement("p");
            if (linha.includes("<p>")) {
              p.innerHTML = linha;
            } else {
              p.textContent = linha;
            }
            conteudo.appendChild(p);
          }
        });
      } catch (erro) {
        console.error(erro);
        document.getElementById("titulo").textContent = "Erro ao carregar o conteúdo.";
      }
    }

    carregarConteudo();
  </script>

  <script>
    function adicionarCompartilhamento() {
      const linkCurto = "<?php echo isset($short_link) ? $short_link : 'https://ieccp.com.br'; ?>"
      const titulo = "<?php echo isset($page_title) ? $page_title : 'IECCP'; ?>";

      if (navigator.share) {
        navigator.share({
          title: titulo,
          text: 'Confira essa publicação da IECCP',
          url: linkCurto
        }).catch(console.error);
      } else {
        navigator.clipboard.writeText(linkCurto).then(() => {
          alert("Link copiado: " + linkCurto);
        }).catch(() => {
          prompt("Copie o link: ", linkCurto);
        })
      }
    }
  </script>

  <script src="/scripts/menu.js"></script>
  <script src="/scripts/popup.js"></script>
  <script src="/scripts/tema.js"></script>
</body>

</html>