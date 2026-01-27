document.addEventListener("DOMContentLoaded", () => {
  initNoticias();
  initMissionarios();
  initAgenda();
  initPastoral();
});

// --- NOTÍCIAS E MISSÕES ---

async function initNoticias() {
  const dados = await fetchData("/data/noticias.json");
  if (!dados) return;

  renderizarCards(dados, "container-noticias", "template-padrao", 4);
  renderizarCards(dados, "container-todas-noticias", "template-padrao");
}

async function initMissionarios() {
  const dados = await fetchData("/data/missionarios.json");
  if (dados) {
    renderizarCards(dados, "container-missionarios", "template-missionarios");
  }
}

function renderizarCards(lista, idContainer, idTemplate, maxItems = null) {
  const container = document.getElementById(idContainer);
  const template = document.getElementById(idTemplate);
  if (!container || !template) return;

  container.innerHTML = "";
  const itens = maxItems ? lista.slice(0, maxItems) : lista;

  itens.forEach((item) => {
    const clone = template.content.cloneNode(true);

    // Imagem
    const img = clone.querySelector("img");
    if (img) {
      img.src = formatarImagem(item.img, item.titulo);
      img.alt = item.titulo;
    }

    // Link
    const linkWrap = clone.querySelector(".card-link-wrapper");
    if (linkWrap) {
      linkWrap.href = item.id ? `leitura.html?id=${item.id}` : item.link || "#";
    }

    // Textos
    setText(clone, ".desc", item.titulo);
    setText(clone, ".short-desc", limparTexto(item.descricao));

    container.appendChild(clone);
  });
}

// --- AGENDA ---

async function initAgenda() {
  const container = document.getElementById("container-agenda");
  const template = document.getElementById("template-agenda");
  if (!container || !template) return;

  const eventos = await fetchData("/data/agenda.json");

  container.innerHTML = "";
  if (!eventos || eventos.length === 0) {
    container.innerHTML =
      "<p class='text-center w-100'>Nenhum evento agendado.</p>";
    return;
  }

  eventos.forEach((ev) => {
    const clone = template.content.cloneNode(true);

    // Imagem
    const img = clone.querySelector("img");
    if (img) {
      img.src = formatarImagem(ev.img, ev.titulo);
      img.alt = ev.titulo;
    }

    // Badge de Data
    const badge = clone.querySelector(".date-badge");
    if (badge) {
      ev.data ? (badge.textContent = ev.data) : (badge.style.display = "none");
    }

    // Local e Textos
    setText(clone, ".desc", ev.titulo);
    setText(clone, ".short-desc", ev.texto);
    setText(clone, ".texto-local", ev.local);

    const divLocal = clone.querySelector(".event-location");
    if (divLocal && !ev.local) divLocal.style.display = "none";

    container.appendChild(clone);
  });
}

// --- PASTORAL ---
async function initPastoral() {
  // Busca
  const dados = await fetchData("/data/pastoral.json");
  if (dados) {
    renderizarCards(dados, "container-pastoral", "template-pastoral", 4);
    renderizarCards(dados, "container-todos-pastoral", "template-pastoral");
  }
}

// --- HELPERS (UTILITÁRIOS) ---

async function fetchData(url) {
  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`Erro ao carregar ${url}`);
    return await res.json();
  } catch (err) {
    console.warn(err); // Warn é menos agressivo que Error no console
    return null;
  }
}

function setText(el, selector, text) {
  const target = el.querySelector(selector);
  if (target) target.innerText = text || "";
}

function formatarImagem(src, alt) {
  if (!src) return "img/logo.png";
  // Remove caminhos relativos (../) e domínios absolutos para evitar CORS/404
  return src.replace("../", "").replace(/^https?:\/\/ieccp\.com\.br\//, "");
}

function limparTexto(texto) {
  if (!texto) return "";
  // Decodifica quebras de linha que vêm do PHP/JSON
  return texto.replace(/&#13;/g, "").replace(/&#10;/g, "\n");
}
