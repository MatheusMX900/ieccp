<?php
// leitura.php - Versão Final Limpa
require_once __DIR__ . '/includes/functions.php'; // Usa suas funções de Slug e JSON

// Captura parâmetros
$id = $_GET['id'] ?? null;
$slug = $_GET['slug'] ?? null;
$tipo = $_GET['tipo'] ?? 'noticia';

// Configurações Padrão
$page_title = "Leitura - IECCP";
$page_desc = "Conteúdo da IECCP.";
$page_img = "https://ieccp.com.br/img/logo.png";
$page_url = "https://ieccp.com.br/";
$dados = null;

// --- 1. LÓGICA DO PRESENTE DIÁRIO (Banco de Dados) ---
if ($tipo == 'pd') {
  require_once __DIR__ . '/includes/db.php';
  if (isset($pdo)) {
    if ($slug) {
      // Se o banco não tiver coluna 'slug', buscamos pelo título gerando slug na hora (menos performático mas funciona sem migration)
      // Se você já criou coluna slug, use: WHERE slug = :slug
      $stmt = $pdo->query("SELECT * FROM presente_diario ORDER BY data_publicacao DESC");
      $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
      foreach ($todos as $row) {
        if (gerarSlug($row['titulo']) === $slug) {
          $dados = $row;
          break;
        }
      }
    } elseif ($id) {
      $stmt = $pdo->prepare("SELECT * FROM presente_diario WHERE id = :id");
      $stmt->execute([':id' => $id]);
      $dados = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if ($dados) {
      $page_title = $dados['titulo'];
      $page_desc = resumirTexto($dados['conteudo']);
      $page_img = !empty($dados['imagem']) ? $dados['imagem'] : "https://ieccp.com.br/img/capa-padrao-pd.jpg";
      $dados['conteudo_html'] = $dados['conteudo'];
      $dados['meta_data'] = date('d/m/Y', strtotime($dados['data_publicacao']));
      $dados['meta_autor'] = $dados['autor'];
      $page_url = "https://ieccp.com.br/pd/" . gerarSlug($dados['titulo']);
    }
  }
}

// --- 2. LÓGICA DE JSON (Notícias, Pastoral, etc) ---
else {
  $arquivo = 'noticias.json';
  if ($tipo == 'pastoral') $arquivo = 'pastoral.json';
  if ($tipo == 'agenda') $arquivo = 'agenda.json';

  $lista = lerJson($arquivo);

  // Percorre o JSON procurando o item certo
  foreach ($lista as $item) {
    // Gera o slug deste item para comparar com a URL
    $slugItem = gerarSlug($item['titulo']);

    // Verifica se bate pelo ID (link antigo) OU pelo Slug (link novo)
    if (($id && $item['id'] == $id) || ($slug && $slugItem === $slug)) {
      $dados = $item;
      $page_title = $item['titulo'];
      $textoFull = $item['texto'] ?? $item['descricao'] ?? $item['conteudo'] ?? "";
      $page_desc = resumirTexto($textoFull);
      $page_img = formatarImagem($item['img'] ?? '');

      // Tratamento de texto
      if (strpos($textoFull, '<p>') === false) {
        $dados['conteudo_html'] = nl2br($textoFull);
      } else {
        $dados['conteudo_html'] = $textoFull;
      }

      $dados['meta_data'] = $item['data'] ?? '';
      $dados['meta_autor'] = ucfirst($tipo);

      // Define a URL canônica
      $prefixo = ($tipo == 'agenda') ? 'evento' : $tipo;
      $page_url = "https://ieccp.com.br/$prefixo/" . $slugItem;
      break;
    }
  }
}
?>

<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="image/svg+xml" href="/img/favicon2.png" />

  <title><?php echo $page_title; ?></title>

  <meta property="og:type" content="article" />
  <meta property="og:title" content="<?php echo $page_title; ?>" />
  <meta property="og:description" content="<?php echo $page_desc; ?>" />
  <meta property="og:image" content="<?php echo $page_img; ?>" />
  <meta property="og:url" content="<?php echo $page_url; ?>" />
  <meta property="og:site_name" content="IECCP" />

  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?php echo $page_title; ?>" />
  <meta name="twitter:description" content="<?php echo $page_desc; ?>" />
  <meta name="twitter:image" content="<?php echo $page_img; ?>" />

  <link rel="stylesheet" href="/styles/global.css" />
  <link rel="stylesheet" href="/styles/header.css" />
  <link rel="stylesheet" href="/styles/gallery.css" />
  <link rel="stylesheet" href="/styles/agenda.css" />
  <link rel="stylesheet" href="/styles/leitura.css" />

  <link href="https://fonts.googleapis.com/css?family=Oswald:400,500,700" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
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
        <li><a href="/missoes">Missões</a></li>
        <li><button id="btn-tema" title="Mudar Tema"><i class="fa-solid fa-sun"></i></button></li>
      </ul>
    </nav>
  </header>

  <main>
    <article class="news-container" aria-labelledby="titulo">

      <nav aria-label="Navegação" class="news-nav">
        <?php if ($tipo == 'pd'): ?>
          <a href="/presente-diario" class="back-link">← Voltar para Presente Diário</a>
        <?php else: ?>
          <a href="/<?php echo $tipo == 'agenda' ? 'agenda' : ($tipo == 'pastoral' ? 'pastoral' : 'noticias'); ?>" class="back-link">
            ← Voltar para <?php echo ucfirst($tipo); ?>
          </a>
        <?php endif; ?>
      </nav>

      <?php if ($dados): ?>

        <h1 id="titulo" class="news-title"><?php echo $dados['titulo']; ?></h1>

        <div class="news-meta">
          <time id="data">
            <i class="fa-regular fa-calendar"></i> <?php echo $dados['meta_data']; ?>
          </time>
          <span>• <?php echo htmlspecialchars($dados['meta_autor']); ?></span>

          <button onclick="adicionarCompartilhamento()" class="btn-share" title="Compartilhar">
            <i class="fa-solid fa-share-nodes"></i> Compartilhar
          </button>
        </div>

        <?php if (!empty($page_img) && strpos($page_img, 'logo.png') === false): ?>
          <figure id="figure">
            <img src="<?php echo $page_img; ?>" alt="<?php echo $dados['titulo']; ?>" />
          </figure>
        <?php endif; ?>

        <?php if (!empty($dados['audio'])): ?>
          <div class="audio-box">
            <div class="audio-header">
              <i class="fa-solid fa-headphones"></i> Ouça a mensagem
            </div>
            <audio controls>
              <source src="<?php echo $dados['audio']; ?>" type="audio/mpeg">
              Seu navegador não suporta áudio.
            </audio>
            <div style="text-align: right;">
              <a href="<?php echo $dados['audio']; ?>" target="_blank" download class="download-link">
                <i class="fa-solid fa-download"></i> Baixar MP3
              </a>
            </div>
          </div>
        <?php endif; ?>

        <section id="conteudo" class="news-content">

          <?php if (!empty($dados['versiculo_chave'])): ?>
            <div class="versiculo-chave">
              "<?php echo $dados['versiculo_chave']; ?>"
              <span class="versiculo-ref">📖 <?php echo $dados['referencia_biblica']; ?></span>
            </div>
          <?php endif; ?>

          <?php echo $dados['conteudo_html']; ?>

          <?php if (!empty($dados['youversionLink'])): ?>
            <div class="youversion-box">
              <a href="<?php echo $dados['youversionLink']; ?>" target="_blank" class="youversion-link">
                Ler em YouVersion <i class="fa-solid fa-external-link-alt"></i>
              </a>
            </div>
          <?php endif; ?>

          <?php if ($tipo == 'pd'): ?>
            <div class="source-box" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; font-size: 0.85rem; color: #666;">
              <p style="margin-bottom: 5px;">Conteúdo original provido por RTM Brasil.</p>
              <a href="https://presentediario.rtmbrasil.org.br/" target="_blank" rel="noopener noreferrer" style="color: #005fcc; font-weight: 600; text-decoration: none;">
                <i class="fa-solid fa-up-right-from-square"></i> Acessar site oficial do Presente Diário
              </a>
            </div>
          <?php endif; ?>

        </section>

      <?php else: ?>

        <div style="text-align: center; padding: 60px 20px;">
          <h1 class="news-title">Conteúdo não encontrado</h1>
          <p>O link que você tentou acessar não existe ou foi removido.</p>
          <br>
          <a href="/" class="btn-share" style="text-decoration:none;">Voltar ao Início</a>
        </div>

      <?php endif; ?>

    </article>
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

  <script>
    function adicionarCompartilhamento() {
      const linkCurto = window.location.href;
      const titulo = document.title;

      if (navigator.share) {
        navigator.share({
          title: titulo,
          text: 'Confira essa mensagem da IECCP',
          url: linkCurto
        }).catch(console.error);
      } else {
        navigator.clipboard.writeText(linkCurto).then(() => {
          alert("Link copiado!");
        }).catch(() => {
          prompt("Copie o link: ", linkCurto);
        })
      }
    }
  </script>

  <script src="/scripts/menu.js"></script>
  <script src="/scripts/tema.js"></script>
</body>

</html>