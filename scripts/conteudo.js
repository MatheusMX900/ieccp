async function fetchNoticias() {
  try {
    const resposta = await fetch("https://ieccp.com.br/data/noticias.json");
    if (!resposta.ok) throw new Error("Erro ao carregar JSON");
    const dados = await resposta.json();

    criarCarrossel(dados, "container-noticias", "template-padrao", 4);
    criarCarrossel(dados, "container-todas-noticias", "template-padrao");
  } catch (erro) {
    console.error("Erro no carregamento das notícias:", erro);
  }
}

async function fetchMissionarios() {
  try {
    const resposta = await fetch("https://ieccp.com.br/data/missionarios.json");
    if (!resposta.ok) throw new Error("Erro ao carregar JSON");
    const dados = await resposta.json();

    criarCarrossel(dados, "container-missionarios", "template-missionarios");
  } catch (erro) {
    console.error("Erro no carregamento dos missionários:", erro);
  }
}

function criarCarrossel(
  listaDeDados,
  idContainer,
  idTemplate,
  quantidadeMaxima,
) {
  const container = document.getElementById(idContainer);
  if (!container) {
    return;
  }

  const template = document.getElementById(idTemplate);
  if (!template) {
    console.error(`Template com id "${idTemplate}" não encontrado.`);
    return;
  }

  container.innerHTML = "";

  const listaFinal = quantidadeMaxima
    ? listaDeDados.slice(0, quantidadeMaxima)
    : listaDeDados;

  listaFinal.forEach((dado) => {
    const clone = template.content.cloneNode(true);

    // 1. Acha a imagem
    const img = clone.querySelector("img");
    if (img) {
      img.src = dado.img;
      img.alt = dado.titulo;
    }

    const linkWrapper = clone.querySelector(".card-link-wrapper");
    if (linkWrapper) {
      if (dado.id) {
        linkWrapper.href = `leitura.html?id=${dado.id}`;
      } else {
        linkWrapper.href = dado.link || "#";
      }
    }

    // 3. Acha o título
    const titulo = clone.querySelector(".desc");
    if (titulo) {
      titulo.textContent = dado.titulo;
    }

    const descricao = clone.querySelector(".short-desc");
    if (descricao) {
      descricao.textContent = dado.descricao || "";
    }

    container.appendChild(clone);
  });
}

// Inicia
fetchNoticias();
fetchMissionarios();
