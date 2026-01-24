/* scripts/conteudo.js */

async function fetchNoticias() {
  try {
    const resposta = await fetch("data/noticias.json");
    if (!resposta.ok) throw new Error("Erro ao carregar JSON");
    const dados = await resposta.json();

    // Configura os carrosséis
    // Tenta preencher a Home
    criarCarrossel(dados, "container-noticias", 4);
    // Tenta preencher a página de Todas as Notícias
    criarCarrossel(dados, "container-todas-noticias");
  } catch (erro) {
    console.error("Erro no carregamento:", erro);
  }
}

function criarCarrossel(listaDeDados, idContainer, quantidadeMaxima) {
  const container = document.getElementById(idContainer);
  const template = document.getElementById("template-padrao");

  // Se não achar o container ou o template, sai da função sem erro
  if (!container || !template) return;

  container.innerHTML = "";

  const listaFinal = quantidadeMaxima
    ? listaDeDados.slice(0, quantidadeMaxima)
    : listaDeDados;

  listaFinal.forEach((dado) => {
    // Clona o modelo HTML
    const clone = template.content.cloneNode(true);

    // 1. Acha a imagem dentro da caixinha .card-image-box
    const img = clone.querySelector("img");
    if (img) {
      img.src = dado.img;
      img.alt = dado.titulo;
    }

    // 2. Acha o link que abraça tudo
    const linkWrapper = clone.querySelector(".card-link-wrapper");
    if (linkWrapper) {
      linkWrapper.href = dado.link;
    }

    // 3. Acha o título
    const titulo = clone.querySelector(".desc");
    if (titulo) {
      titulo.textContent = dado.titulo;
    }

    container.appendChild(clone);
  });
}

// Inicia
fetchNoticias();
