// Initialisation des graphiques
document.addEventListener("DOMContentLoaded", function () {
  if (typeof statsData === "undefined") {
    console.error("Les données statsData ne sont pas disponibles");
    return;
  }

  // Options partagées pour forcer les graphiques à respecter le conteneur HTML
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
  };

  // --- Graphique 1 : CA par Menu ---
  const ctxCa = document.getElementById("caMenuChart").getContext("2d");
  new Chart(ctxCa, {
    type: "bar",
    data: {
      labels: statsData.map((item) => item.titreMenu),
      datasets: [
        {
          label: "CA (€)",
          data: statsData.map((item) => item.caTotalMenu),
          backgroundColor: "rgba(54, 162, 235, 0.6)",
        },
      ],
    },
    options: chartOptions,
  });

  // --- Graphique 2 : Volume des commandes ---
  const ctxOrders = document.getElementById("ordersChart").getContext("2d");
  new Chart(ctxOrders, {
    type: "doughnut",
    data: {
      labels: statsData.map((item) => item.titreMenu),
      datasets: [
        {
          label: "Nombre de commandes",
          data: statsData.map((item) => item.nombreCommandes),
          backgroundColor: [
            "#FF6384",
            "#36A2EB",
            "#FFCE56",
            "#4BC0C0",
            "#9966FF",
          ],
        },
      ],
    },
    options: chartOptions, // Indispensable ici pour que le doughnut respecte les 280px du conteneur
  });
});
