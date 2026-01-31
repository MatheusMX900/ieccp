document.addEventListener("DOMContentLoaded", () => {
  initNoticias();
  initMissionarios();
  initAgenda();
  initPastoral();
});

// --- NOTÍCIAS E MISSÕES ---

async function initNoticias() {
  const dados = await fetchData("data/noticias.json");
  if (!dados) return;

  renderizarCards(dados, "container-noticias", "template-padrao", 4);
  renderizarCards(dados, "container-todas-noticias", "template-padrao");
}

async function initMissionarios() {
  const dados = await fetchData("data/missionarios.json");
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

    const img = clone.querySelector("img");
    if (img) {
      img.src = formatarImagem(item.img, item.titulo);
      img.alt = item.titulo;
    }

    const linkWrap = clone.querySelector(".card-link-wrapper");
    if (linkWrap) {
      linkWrap.href = item.id ? `leitura.html?id=${item.id}` : item.link || "";
      linkWrap.setAttribute("aria-label", `Ler conteúdo: ${item.titulo}`);
    }

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

  const eventos = await fetchData("data/agenda.json");

  container.innerHTML = "";
  if (!eventos || eventos.length === 0) {
    container.innerHTML =
      "<p class='text-center w-100'>Nenhum evento agendado.</p>";
    return;
  }

  eventos.forEach((ev) => {
    const clone = template.content.cloneNode(true);

    const img = clone.querySelector("img");
    if (img) {
      img.src = formatarImagem(ev.img, ev.titulo);
      img.alt = ev.titulo;
    }

    const badge = clone.querySelector(".date-badge");
    if (badge) {
      ev.data ? (badge.textContent = ev.data) : (badge.style.display = "none");
    }

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
  const dados = await fetchData("data/pastoral.json");
  if (dados) {
    renderizarCards(dados, "container-pastoral", "template-pastoral", 4);
    renderizarCards(dados, "container-todos-pastoral", "template-pastoral");
  }
}

// --- HELPERS ---

async function fetchData(url) {
  try {
    const res = await fetch(url);
    if (!res.ok) throw new Error(`Erro ao carregar ${url}`);
    return await res.json();
  } catch (err) {
    console.warn(err);
    return null;
  }
}

function setText(el, selector, text) {
  const target = el.querySelector(selector);
  if (target) target.innerText = text || "";
}

function formatarImagem(src) {
  if (!src) return "img/logo.png";
  return src.replace("../", "").replace(/^https?:\/\/ieccp\.com\.br\//, "");
}

function limparTexto(texto) {
  if (!texto) return "";
  return texto.replace(/&#13;/g, "").replace(/&#10;/g, "\n");
}

// --- FEED DE NOTÍCIAS (noticias.html) ---

async function carregarFeedNoticias() {
  const container = document.getElementById("feed-container");
  const template = document.getElementById("template-noticia");
  if (!container || !template) return;

  try {
    const response = await fetch("data/noticias.json");
    const noticias = await response.json();

    container.innerHTML = "";

    if (!noticias.length) {
      container.innerHTML =
        "<p style='text-align:center'>Nenhuma notícia encontrada!</p>";
      return;
    }

    noticias.forEach((item) => {
      const clone = template.content.cloneNode(true);

      const link = clone.querySelector(".noticia-link");
      link.href = `leitura.html?id=${item.id}`;
      link.setAttribute("aria-label", `Ler notícia: ${item.titulo}`);

      const dataBadge = clone.querySelector(".data-badge");
      if (dataBadge) dataBadge.textContent = item.data || "";

      const titulo = clone.querySelector("h2");
      if (titulo) titulo.textContent = item.titulo;

      const divTexto = clone.querySelector(".texto-dinamico");
      if (divTexto && item.texto) {
        item.texto.split("\n").forEach((pTxt) => {
          if (pTxt.trim()) {
            const p = document.createElement("p");
            p.textContent = pTxt;
            divTexto.appendChild(p);
          }
        });
      }

      const img = clone.querySelector("img");
      const divImg = clone.querySelector(".conteudo-imagem");

      if (item.img) {
        img.src = formatarImagem(item.img);
        img.alt = item.titulo;
      } else if (divImg) {
        divImg.remove();
      }

      container.appendChild(clone);
    });
  } catch (error) {
    console.error("Erro feed:", error);
  }
}
