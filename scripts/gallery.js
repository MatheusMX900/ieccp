const dadosNoticias = [
    { titulo: "Culto de Domingo", img: "https://picsum.photos/600/600?random=1", link: "#" },
    { titulo: "Evento Jovem", img: "https://picsum.photos/600/600?random=2", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=3", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=4", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=5", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=6", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=7", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=8", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=9", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=10", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=11", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=12", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=13", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=14", link: "#" }
];

const dadosMissionarios = [
    { titulo: "Família Silva", img: "https://picsum.photos/600/600?random=4", link: "#" },
    { titulo: "Missionário João", img: "https://picsum.photos/600/600?random=5", link: "#" },
    { titulo: "Projeto África", img: "https://picsum.photos/600/600?random=6", link: "#" }
];

function criarCarrossel(listaDeDados, idContainer) {
    
    const container = document.getElementById(idContainer);
    const template = document.getElementById('template-padrao');

    // Verificação de segurança
    if (!container || !template) {
        console.error(`Erro: Não achei o container '${idContainer}' ou o template.`);
        return;
    }

    listaDeDados.forEach(dado => {
        const clone = template.content.cloneNode(true);

        // Preenchendo os campos
        const img = clone.querySelector('img');
        img.src = dado.img;
        img.alt = dado.titulo;

        clone.querySelector('.desc').textContent = dado.titulo;
        clone.querySelector('a').href = dado.link;

        // Adiciona ao container específico
        container.appendChild(clone);
    });
}

criarCarrossel(dadosNoticias, 'container-noticias');
criarCarrossel(dadosMissionarios, 'container-missionarios');