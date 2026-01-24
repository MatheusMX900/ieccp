// scripts/tema.js

const toggleBtn = document.getElementById("btn-tema"); // Usei o ID correto que está no seu HTML
const body = document.body;

// 1. CARREGAR PREFERÊNCIA
// Como o padrão (CSS) já é escuro, só precisamos agir se o usuário salvou 'light'
const temaSalvo = localStorage.getItem("temaPreferido");

if (temaSalvo === "light") {
  body.classList.add("light-mode");
}

// 2. FUNÇÃO DE ALTERNAR
if (toggleBtn) {
  toggleBtn.addEventListener("click", () => {
    // Alterna a classe 'light-mode'
    body.classList.toggle("light-mode");

    // Salva a decisão na memória
    if (body.classList.contains("light-mode")) {
      localStorage.setItem("temaPreferido", "light");
      // Opcional: Mudar ícone para Lua
      toggleBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
    } else {
      localStorage.setItem("temaPreferido", "dark");
      // Opcional: Mudar ícone para Sol
      toggleBtn.innerHTML = '<i class="fa-solid fa-sun"></i>';
    }
  });

  // Ajustar ícone inicial se carregar light
  if (temaSalvo === "light") {
    toggleBtn.innerHTML = '<i class="fa-solid fa-moon"></i>';
  }
}
