document.addEventListener("DOMContentLoaded", function () {
  // 1. Mudei o nome da chave para "_v2". Isso RESETA a decisão de quem clicou "Não" antes.
  const STORAGE_KEY = "decisaoNotificacao_v2";
  const DIAS_PARA_ESQUECER = 7; // Reduzi para 7 dias para testar mais rápido (ajuste se quiser)

  const modal = document.getElementById("modal-notificacao");
  const btnAceitar = document.getElementById("btn-aceitar");
  const btnRecusar = document.getElementById("btn-agora-nao");

  if (!modal || !btnAceitar || !btnRecusar) return;

  // --- FUNÇÃO PRINCIPAL DE VERIFICAÇÃO ---
  function gerenciarExibicaoModal() {
    // Passo A: Verifica se o OneSignal já está carregado e se o usuário JÁ É inscrito
    if (window.OneSignalDeferred) {
      window.OneSignalDeferred.push(function (OneSignal) {
        // Se o usuário já permitiu notificações no navegador (mesmo que tenha limpado cookies)
        if (OneSignal.User.PushSubscription.optedIn) {
          console.log("Usuário já é inscrito. Não mostrar modal.");
          // Salvamos no storage para evitar verificações futuras desnecessárias
          localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify({ status: "aceitou", timestamp: Date.now() }),
          );
          return;
        }

        // Se não é inscrito, verificamos o histórico de recusa
        verificarHistoricoRecusa();
      });
    } else {
      // Fallback se o OneSignal demorar a carregar
      verificarHistoricoRecusa();
    }
  }

  function verificarHistoricoRecusa() {
    const registro = localStorage.getItem(STORAGE_KEY);

    // Se não tem registro (nunca viu ou limpamos com o v2), MOSTRA.
    if (!registro) {
      mostrarModal();
      return;
    }

    let dados;
    try {
      dados = JSON.parse(registro);
    } catch (e) {
      localStorage.removeItem(STORAGE_KEY);
      mostrarModal();
      return;
    }

    // Se ele já clicou em "Sim" (e não é inscrito, verificado no passo A), talvez tenha bloqueado no navegador.
    // Nesse caso, respeitamos e não mostramos.
    if (dados.status === "aceitou") return;

    // Se ele clicou em "Agora não", verificamos se já passou o tempo
    if (dados.status === "recusou") {
      const dataRecusa = new Date(dados.timestamp);
      const hoje = new Date();
      const diferencaEmDias =
        (hoje.getTime() - dataRecusa.getTime()) / (1000 * 3600 * 24);

      if (diferencaEmDias > DIAS_PARA_ESQUECER) {
        mostrarModal();
      }
    }
  }

  function mostrarModal() {
    setTimeout(() => {
      modal.style.display = "flex";
    }, 2000);
  }

  // --- BOTÃO ACEITAR ---
  btnAceitar.addEventListener("click", function () {
    modal.style.display = "none"; // Fecha visualmente na hora

    window.OneSignalDeferred.push(function (OneSignal) {
      OneSignal.User.PushSubscription.optIn().then((accepted) => {
        if (accepted) {
          // Salvamos que aceitou
          localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify({ status: "aceitou", timestamp: Date.now() }),
          );
          console.log("Inscrito com sucesso!");
        } else {
          // Se ele fechar o prompt do navegador sem aceitar, tratamos como "recusou"
          console.log("Fechou ou bloqueou o prompt nativo.");
          localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify({ status: "recusou", timestamp: Date.now() }),
          );
        }
      });
    });
  });

  // --- BOTÃO RECUSAR ---
  btnRecusar.addEventListener("click", function () {
    const dados = { status: "recusou", timestamp: Date.now() };
    localStorage.setItem(STORAGE_KEY, JSON.stringify(dados));
    modal.style.display = "none";
  });

  // Inicia a lógica
  gerenciarExibicaoModal();
});

// --- Cookie Banner (Mantive igual) ---
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
