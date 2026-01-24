function toggleMenu() {
  const nav = document.getElementById("menu");
  const btn = document.getElementById("btn-mobile");

  // Se não encontrar os elementos, para o código para não dar erro
  if (!nav || !btn) return;

  // Alterna a classe 'active' no menu (para ele deslizar na tela)
  nav.classList.toggle("active");

  // Alterna a classe 'active' no botão (para as linhas virarem X)
  btn.classList.toggle("active");
}

/**
 * 2. EVENTOS QUE CARREGAM JUNTO COM A PÁGINA
 */
document.addEventListener("DOMContentLoaded", () => {
  /* --- FECHAR MENU AO CLICAR EM UM LINK --- */
  // Seleciona todos os links dentro do menu
  const links = document.querySelectorAll("#menu li a");

  links.forEach((link) => {
    link.addEventListener("click", () => {
      const nav = document.getElementById("menu");
      const btn = document.getElementById("btn-mobile");

      // Remove a classe 'active' para fechar tudo suavemente
      if (nav) nav.classList.remove("active");
      if (btn) btn.classList.remove("active");
    });
  });

  /* --- LÓGICA DO BOTÃO VOLTAR AO TOPO --- */
  const btnTop = document.getElementById("back-to-top");

  // Só executa se o botão existir na página (para não dar erro)
  if (btnTop) {
    // Evento de Rolagem (Scroll)
    window.addEventListener("scroll", () => {
      // Se rolou mais de 300 pixels para baixo, mostra o botão
      if (window.scrollY > 300) {
        btnTop.classList.add("show");
      } else {
        btnTop.classList.remove("show");
      }
    });

    // Evento de Clique (Subir)
    btnTop.addEventListener("click", (e) => {
      e.preventDefault(); // Evita que o # apareça na URL
      window.scrollTo({
        top: 0,
        behavior: "smooth", // Faz a subida ser suave e elegante
      });
    });
  }
});
