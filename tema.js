const btnTema = document.getElementById('btn-tema');
const body = document.body;

// Códigos dos ícones (para facilitar a troca)
const iconSol = '<i class="fa-solid fa-sun"></i>';
const iconLua = '<i class="fa-solid fa-moon"></i>';

if (btnTema) {
    // 1. Checar preferência salva
    const temaSalvo = localStorage.getItem('tema');
    
    if (temaSalvo === 'claro') {
        body.classList.add('light-mode');
        btnTema.innerHTML = iconLua; // Se tá claro, mostra a lua
    } else {
        btnTema.innerHTML = iconSol; // Padrão (escuro) mostra o sol
    }

    // 2. Evento de clique
    btnTema.addEventListener('click', () => {
        body.classList.toggle('light-mode');

        if (body.classList.contains('light-mode')) {
            localStorage.setItem('tema', 'claro');
            btnTema.innerHTML = iconLua; // Troca para Lua
        } else {
            localStorage.setItem('tema', 'escuro');
            btnTema.innerHTML = iconSol; // Troca para Sol
        }
    });
}