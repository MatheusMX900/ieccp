const toggleBtn = document.getElementById("btn-tema");
const body = document.body;

// Ícones para facilitar a manutenção
const iconSun = '<i class="fa-solid fa-sun"></i>';
const iconMoon = '<i class="fa-solid fa-moon"></i>';

// 1. Verifica se há um tema salvo ou se o sistema prefere "light"
const temaSalvo = localStorage.getItem("temaPreferido");
const prefereLightSistema = window.matchMedia(
  "(prefers-color-scheme: light)",
).matches;

// 2. Define o estado inicial (Prioridade: LocalStorage > Sistema > Padrão Dark)
if (temaSalvo === "light" || (!temaSalvo && prefereLightSistema)) {
  body.classList.add("light-mode");
  if (toggleBtn) toggleBtn.innerHTML = iconMoon;
} else {
  if (toggleBtn) toggleBtn.innerHTML = iconSun;
}

// 3. Lógica do botão de alternância
if (toggleBtn) {
  toggleBtn.addEventListener("click", () => {
    body.classList.toggle("light-mode");

    const isLight = body.classList.contains("light-mode");
    localStorage.setItem("temaPreferido", isLight ? "light" : "dark");
    toggleBtn.innerHTML = isLight ? iconMoon : iconSun;
  });
}
