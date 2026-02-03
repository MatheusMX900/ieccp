document.addEventListener("DOMContentLoaded", () => {
  initNoticias();
  initMissionarios();
  initAgenda();
  initPastoral();

  // Se estiver na página de feed de notícias
  if (document.getElementById("feed-container")) {
    carregarFeedNoticias();
  }
});

// --- FUNÇÃO DE BUSCA (COM ANTI-CACHE) ---
async function fetchData(url) {
  try {
    // Adiciona timestamp para forçar o navegador a pegar a versão nova do arquivo
    const urlComCache = `${url}?v=${Date.now()}`;
    const res = await fetch(urlComCache);
    if (!res.ok) throw new Error(`Erro ao carregar ${url}`);
    return await res.json();
  } catch (err) {
    console.warn(`Erro ou lista vazia em ${url}:`, err);
    return [];
  }
}

// --- PASTORAL (AQUI ESTÁ A CORREÇÃO) ---
async function initPastoral() {
  const dados = await fetchData("data/pastoral.json");

  if (dados && dados.length > 0) {
    // 1. Carrega na Home (limite de 4)
    renderizarCards(dados, "container-pastoral", "template-pastoral", 4);

    // 2. Carrega na página pastoral.html (TODOS) - Este é o que faltava carregar
    renderizarCards(dados, "container-todos-pastoral", "template-pastoral");
  }
}

// --- OUTRAS INICIALIZAÇÕES ---
async function initNoticias() {
  const dados = await fetchData("data/noticias.json");
  if (!dados) return;
  renderizarCards(dados, "container-noticias", "template-padrao", 4);
  renderizarCards(dados, "container-todas-noticias", "template-padrao");
}

async function initMissionarios() {
  const dados = await fetchData("data/missionarios.json");
  if (dados)
    renderizarCards(dados, "container-missionarios", "template-missionarios");
}

async function initAgenda() {
  const dados = await fetchData("data/agenda.json");
  const container = document.getElementById("container-agenda");
  const template = document.getElementById("template-agenda");

  if (!container || !template) return;
  container.innerHTML = "";

  if (!dados || dados.length === 0) {
    container.innerHTML =
      "<p class='text-center w-100'>Nenhum evento agendado.</p>";
    return;
  }

  dados.slice(0, 6).forEach((ev) => {
    const clone = template.content.cloneNode(true);

    // Imagem
    const img = clone.querySelector("img");
    if (img) {
      img.src = formatarImagem(ev.img);
      img.alt = ev.titulo;
    }

    // Data e Texto
    setText(clone, ".date-badge", ev.data);
    setText(clone, ".desc", ev.titulo);
    setText(clone, ".texto-local", ev.local);

    // Remove descrição da agenda para ficar padrão
    const descEl = clone.querySelector(".short-desc");
    if (descEl) descEl.style.display = "none";

    // Link
    const linkWrap = clone.querySelector(".card-link-wrapper");
    if (linkWrap && ev.id)
      linkWrap.href = `leitura.html?id=${ev.id}&tipo=agenda`;

    container.appendChild(clone);
  });
}

// --- RENDERIZADOR UNIVERSAL ---
function renderizarCards(lista, idContainer, idTemplate, maxItems = null) {
  const container = document.getElementById(idContainer);
  const template = document.getElementById(idTemplate);
  if (!container || !template) return;

  container.innerHTML = "";
  const safeList = Array.isArray(lista) ? lista : [];
  const itens = maxItems ? safeList.slice(0, maxItems) : safeList;

  itens.forEach((item) => {
    const clone = template.content.cloneNode(true);

    const img = clone.querySelector("img");
    if (img) {
      img.src = formatarImagem(item.img);
      img.alt = item.titulo || "Imagem";
      img.onerror = function () {
        this.src = "img/logo.png";
      };
    }

    const linkWrap = clone.querySelector(".card-link-wrapper");
    if (linkWrap) {
      if (item.id) {
        let tipo = "noticia";
        if (idContainer.includes("pastoral")) tipo = "pastoral";
        if (idContainer.includes("agenda")) tipo = "agenda";

        linkWrap.href = `leitura.html?id=${item.id}&tipo=${tipo}`;
      } else {
        linkWrap.href = item.link || "#";
      }
      linkWrap.setAttribute("aria-label", `Ler: ${item.titulo}`);
    }

    setText(clone, ".desc", item.titulo);

    // Remove descrição curta da listagem
    const descEl = clone.querySelector(".short-desc");
    if (descEl) descEl.remove();

    container.appendChild(clone);
  });
}

// --- FEED DE NOTÍCIAS COMPLETO ---
async function carregarFeedNoticias() {
  // (O mesmo código que te passei antes para noticias.html, mantido aqui)
  const container = document.getElementById("feed-container");
  const template = document.getElementById("template-noticia");
  if (!container || !template) return;

  const dados = await fetchData("data/noticias.json");
  container.innerHTML = "";
  if (!dados || !dados.length) {
    container.innerHTML = "<p>Nenhuma notícia.</p>";
    return;
  }

  dados.forEach((item) => {
    const clone = template.content.cloneNode(true);
    // ... (preenchimento padrão) ...
    const link = clone.querySelector(".noticia-link");
    if (link) link.href = `leitura.html?id=${item.id}&tipo=noticia`;
    setText(clone, "h2", item.titulo);
    setText(clone, ".data-badge", item.data);

    const img = clone.querySelector("img");
    if (item.img && img) img.src = formatarImagem(item.img);

    const divTexto = clone.querySelector(".texto-dinamico");
    const texto = item.texto || item.descricao || "";
    if (divTexto) divTexto.innerText = texto.substring(0, 200) + "..."; // Resumo simples

    container.appendChild(clone);
  });
}

// --- HELPERS ---
function setText(el, selector, text) {
  const target = el.querySelector(selector);
  if (target) target.innerText = text || "";
}

function formatarImagem(src) {
  if (!src) return "img/logo.png";
  return src.replace("../", "").replace(/^https?:\/\/ieccp\.com\.br\//, "");
}
