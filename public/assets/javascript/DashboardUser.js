// Sélectionne tous les boutons d'annulation de commande
document.querySelectorAll(".btn-erase-order").forEach((btn) => {
  btn.addEventListener("click", async (e) => {
    e.preventDefault();
    const commandeId = btn.dataset.commandeId;

    const confirmed = await showConfirm(
      "Êtes-vous sûr de vouloir annuler cette commande ?",
    );

    if (confirmed) {
    fetch("index.php?page=erase-order&commande_id=" + commandeId, {
    method: "POST",
    body: new URLSearchParams({
        csrf_token: document.querySelector('input[name="csrf_token"]').value
    })
})
        .then((r) => r.json())
        .then((data) => {
          showToast(data.message, data.success ? "success" : "error");

          window.setTimeout(() => {
            location.reload();
          }, 900);
        });
    }
  });
});
// Gestion de l'affichage de l'alerte de succès
document.addEventListener("DOMContentLoaded", function () {
  const alertElement = document.getElementById("success-alert");

  if (alertElement) {
    // Au bout de 4 secondes (4000 ms)
    setTimeout(function () {
      // 1. Masquer l'alerte
      alertElement.style.transition = "opacity 0.5s ease";
      alertElement.style.opacity = "0";

      setTimeout(function () {
        alertElement.remove();
      }, 500);

      // 2. Nettoyer l'URL (retire "success=1")
      const url = new URL(window.location.href);
      url.searchParams.delete("success");
      window.history.replaceState({}, document.title, url.toString());
    }, 4000);
  }
});
