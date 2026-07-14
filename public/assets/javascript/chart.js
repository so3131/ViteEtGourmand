// a injecter dans query apres l'avoir mis dans le html de admin/stats-admin.view.php:
// <div style="max-width: 500px;">
//     <canvas id="myChart"></canvas>
// </div>

const ctx = document.getElementById("myChart");
new Chart(ctx, {
  type: "bar", // Type de graphique : 'bar', 'pie' (camembert), 'line' (courbe)
  data: {
    labels: ["Janvier", "Février", "Mars"],
    datasets: [
      {
        label: "",
        // echo PHP de mes variables de statistiques
        data: [12, 19, 3],
        backgroundColor: ["#4ade80", "#22c55e", "#15803d"],
      },
    ],
  },
});
