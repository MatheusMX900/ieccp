async function fetchNoticias() {
  try {
    // Caminho relativo para a pasta de dados.
    // Se estiver rodando localmente, garanta que a pasta 'data' existe na raiz.
    const resposta = await fetch("dados/noticias.json");

    if (!resposta.ok) throw new Error("Não foi possível carregar o JSON");

    const dados = await resposta.json();

    // Tenta preencher a Home (limite 5)
    criarCarrossel(dados, "container-noticias", 5);

    // Tenta preencher a página de Todas as Notícias (sem limite)
    criarCarrossel(dados, "container-todas-noticias");
  } catch (erro) {
    console.error("Erro ao buscar dados de notícias:", erro);
  }
}

function criarCarrossel(listaDeDados, idContainer, quantidadeMaxima) {
  const container = document.getElementById(idContainer);
  const template = document.getElementById("template-padrao");

  if (!container || !template) return; // Sai silenciosamente se não achar o container (normal)

  const listaFinal = quantidadeMaxima
    ? listaDeDados.slice(0, quantidadeMaxima)
    : listaDeDados;

  listaFinal.forEach((dado) => {
    const clone = template.content.cloneNode(true);

    const img = clone.querySelector("img");
    img.src = dado.img;
    img.alt = dado.titulo;

    clone.querySelector("a").href = dado.link;
    clone.querySelector(".desc").textContent = dado.titulo;

    container.appendChild(clone);
  });
}

fetchNoticias();
