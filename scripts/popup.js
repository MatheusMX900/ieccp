document.addEventListener("DOMContentLoaded", function () {
  const DIAS_PARA_ESQUECER = 15;

  const modal = document.getElementById("modal-notificacao");
  const btnAceitar = document.getElementById("btn-aceitar");
  const btnRecusar = document.getElementById("btn-agora-nao");

  // --- FUNÇÕES AUXILIARES ---

  function deveMostrarModal() {
    const registro = localStorage.getItem("decisaoNotificacao");
    if (!registro) return true;

    const dados = JSON.parse(registro);
    if (dados.status === "aceitou") return false;

    if (dados.status === "recusou") {
      const dataRecusa = new Date(dados.timestamp);
      const hoje = new Date();

      const diferencaEmTempo = hoje.getTime() - dataRecusa.getTime();
      const diferencaEmDias = diferencaEmTempo / (1000 * 3600 * 24);

      if (diferencaEmDias > DIAS_PARA_ESQUECER) {
        return true;
      }
    }

    return false;
  }

  if (deveMostrarModal()) {
    setTimeout(() => {
      modal.style.display = "flex";
    }, 2000);
  }

  // BOTÃO SIM
  btnAceitar.addEventListener("click", function () {
    const dados = {
      status: "aceitou",
      timestamp: new Date().getTime(), // Salva a hora exata
    };
    localStorage.setItem("decisaoNotificacao", JSON.stringify(dados));

    modal.style.display = "none";
    console.log("Aceitou! Futuramente chama o OneSignal.");
    alert("Obrigado! Em breve avisaremos.");
  });

  // BOTÃO NÃO (Agora com validade)
  btnRecusar.addEventListener("click", function () {
    const dados = {
      status: "recusou",
      timestamp: new Date().getTime(), // Marca a hora que ele disse não
    };
    localStorage.setItem("decisaoNotificacao", JSON.stringify(dados));

    modal.style.display = "none";
  });
});
