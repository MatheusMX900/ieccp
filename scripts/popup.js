document.addEventListener("DOMContentLoaded", function () {
  const DIAS_PARA_ESQUECER = 15;

  // Busca os elementos na tela
  const modal = document.getElementById("modal-notificacao");
  const btnAceitar = document.getElementById("btn-aceitar");
  const btnRecusar = document.getElementById("btn-agora-nao");

  if (!modal || !btnAceitar || !btnRecusar) {
    console.warn("Atenção: O HTML do popup não foi encontrado na página.");
    return; // Sai da função
  }

  // --- FUNÇÕES AUXILIARES ---
  function deveMostrarModal() {
    const registro = localStorage.getItem("decisaoNotificacao");
    if (!registro) return true;

    let dados;
    try {
      dados = JSON.parse(registro);
    } catch (e) {
      localStorage.removeItem("decisaoNotificacao");
      return true;
    }

    if (dados.status === "aceitou") return false;

    if (dados.status === "recusou") {
      const dataRecusa = new Date(dados.timestamp);
      const hoje = new Date();
      const diferencaEmDias =
        (hoje.getTime() - dataRecusa.getTime()) / (1000 * 3600 * 24);

      if (diferencaEmDias > DIAS_PARA_ESQUECER) {
        return true;
      }
    }
    return false;
  }

  // --- LÓGICA PRINCIPAL ---
  if (deveMostrarModal()) {
    setTimeout(() => {
      modal.style.display = "flex";
    }, 2000);
  }

  btnAceitar.addEventListener("click", function () {
    const dados = { status: "aceitou", timestamp: new Date().getTime() };
    localStorage.setItem("decisaoNotificacao", JSON.stringify(dados));
    modal.style.display = "none";
    alert("Obrigado! Em breve avisaremos.");
  });

  btnRecusar.addEventListener("click", function () {
    const dados = { status: "recusou", timestamp: new Date().getTime() };
    localStorage.setItem("decisaoNotificacao", JSON.stringify(dados));
    modal.style.display = "none";
  });
});
