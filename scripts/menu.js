// Seleciona os elementos na tela pelo ID
const btnMobile = document.getElementById("btn-mobile");
const tema = document.getElementById("btn-tema");
const menu = document.getElementById("menu");

function toggleMenu(event) {
  if (event.type === "touchstart") event.preventDefault();

  // Adiciona ou Remove a classe 'active' da lista UL
  // Se tem a classe, tira. Se não tem, coloca.
  menu.classList.toggle("active");
}

if (tema) {
  tema.addEventListener("click", () => {
    const menu = document.getElementById("menu");
    menu.classList.remove("active");
  });
}

// Ouve o clique do mouse e o toque do dedo
btnMobile.addEventListener("click", toggleMenu);
btnMobile.addEventListener("touchstart", toggleMenu);

// Fechar ao clicar em qualquer botão
const linksDoMenu = document.querySelectorAll("#menu a");
linksDoMenu.forEach((link) => {
  link.addEventListener("click", () => {
    menu.classList.remove("active");
  });
});
