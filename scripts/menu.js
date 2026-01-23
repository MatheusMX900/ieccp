// Seleciona os elementos na tela pelo ID
const btnMobile = document.getElementById("btn-mobile");
const menu = document.getElementById("menu");

// Função que faz a mágica acontecer
function toggleMenu(event) {
  // Evita que o botão dê aquele "piscada" padrão de toque em alguns celulares
  if (event.type === "touchstart") event.preventDefault();

  // Adiciona ou Remove a classe 'active' da lista UL
  // Se tem a classe, tira. Se não tem, coloca.
  menu.classList.toggle("active");
}

// Ouve o clique do mouse e o toque do dedo
btnMobile.addEventListener("click", toggleMenu);
btnMobile.addEventListener("touchstart", toggleMenu);
