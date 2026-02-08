document.addEventListener("DOMContentLoaded", function () {
  // Mudei para v4 para garantir um recomeço limpo para todos
  const STORAGE_KEY = "decisaoNotificacao_v4";

  const modal = document.getElementById("modal-notificacao");
  const btnAceitar = document.getElementById("btn-aceitar");
  const btnRecusar = document.getElementById("btn-agora-nao");

  if (!modal || !btnAceitar || !btnRecusar) return;

  function gerenciarExibicaoModal() {
    // 1. Verificação Rápida: Se já tem registro no LocalStorage, respeita e para tudo.
    // Isso evita que o modal "pisque" ou apareça se o OneSignal demorar.
    const registro = localStorage.getItem(STORAGE_KEY);
    if (registro) {
      return; // O usuário já decidiu (Sim ou Não), então não fazemos nada.
    }

    // 2. Se não tem registro, verifica se o usuário já é inscrito pelo OneSignal
    // (Caso ele tenha se inscrito em outra página ou limpado o cache mas mantido a permissão)
    if (window.OneSignalDeferred) {
      window.OneSignalDeferred.push(function (OneSignal) {
        if (OneSignal.User.PushSubscription.optedIn) {
          console.log("Usuário já é inscrito (detectado via OneSignal).");
          salvarDecisao("aceitou"); // Salva para não precisar checar de novo
          return;
        }
        // Se não é inscrito e não tem registro: MOSTRA O MODAL
        mostrarModal();
      });
    } else {
      // Fallback: Se o OneSignal falhar, mostra o modal baseado apenas no LocalStorage (que já checamos ser vazio)
      mostrarModal();
    }
  }

  function mostrarModal() {
    // Pequeno delay para não ser invasivo logo que carrega
    setTimeout(() => {
      modal.style.display = "flex";
    }, 2000);
  }

  function salvarDecisao(status) {
    const dados = { status: status, timestamp: Date.now() };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(dados));
  }

  // --- BOTÃO ACEITAR ---
  btnAceitar.addEventListener("click", function () {
    modal.style.display = "none"; // Fecha visualmente NA HORA

    // GRAVA IMEDIATAMENTE: "O usuário clicou em Sim".
    // Não esperamos o OneSignal responder. Assim o modal nunca mais volta.
    salvarDecisao("aceitou");

    // Tenta inscrever no OneSignal
    window.OneSignalDeferred.push(function (OneSignal) {
      OneSignal.User.PushSubscription.optIn().catch((err) => {
        console.error("Erro ao tentar inscrever:", err);
      });
    });
  });

  // --- BOTÃO RECUSAR ---
  btnRecusar.addEventListener("click", function () {
    modal.style.display = "none";
    // Grava "recusou" e nunca mais pergunta
    salvarDecisao("recusou");
  });

  // Inicia a lógica
  gerenciarExibicaoModal();
});

// --- Cookie Banner ---
document.addEventListener("DOMContentLoaded", function () {
  const banner = document.getElementById("cookie-banner");
  const btnCookie = document.getElementById("btn-cookie-ok");
  if (!localStorage.getItem("aceitouCookies") && banner) {
    banner.style.display = "flex";
  }
  if (btnCookie) {
    btnCookie.addEventListener("click", function () {
      localStorage.setItem("aceitouCookies", "true");
      if (banner) banner.style.display = "none";
    });
  }
});
