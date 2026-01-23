function criarCarrossel(listaDeDados, idContainer, quantidadeMaxima) {
  const container = document.getElementById(idContainer);
  const template = document.getElementById("template-padrao");

  // Verificação de segurança
  if (!container || !template) return;

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

criarCarrossel(dadosNoticias, "container-noticias", 5);
criarCarrossel(dadosMissionarios, "container-missionarios");
