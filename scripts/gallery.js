// --- DADOS (Seu "Banco de Dados" simulado) ---

const dadosNoticias = [
    { titulo: "Culto de Domingo", img: "https://picsum.photos/600/600?random=1", link: "#" },
    { titulo: "Evento Jovem", img: "https://picsum.photos/600/600?random=2", link: "#" },
    { titulo: "Ação Social", img: "https://picsum.photos/600/600?random=3", link: "#" }
];

const dadosMissionarios = [
    { titulo: "Família Silva", img: "https://picsum.photos/600/600?random=4", link: "#" },
    { titulo: "Missionário João", img: "https://picsum.photos/600/600?random=5", link: "#" },
    { titulo: "Projeto África", img: "https://picsum.photos/600/600?random=6", link: "#" }
];


// --- A FUNÇÃO MÁGICA (O Motor) ---
// Ela recebe: A lista de dados e o ID de onde deve desenhar
function criarCarrossel(listaDeDados, idContainer) {
    
    const container = document.getElementById(idContainer);
    const template = document.getElementById('template-padrao');

    // Verificação de segurança
    if (!container || !template) {
        console.error(`Erro: Não achei o container '${idContainer}' ou o template.`);
        return; // Para a função aqui se der erro
    }

    // O Loop
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


// --- EXECUTANDO A MÁGICA ---
// Aqui nós chamamos a função 3 vezes, uma para cada seção

criarCarrossel(dadosNoticias, 'container-noticias');
criarCarrossel(dadosMissionarios, 'container-missionarios');