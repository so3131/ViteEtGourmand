// Ce script gère l'affichage conditionnel du conteneur des conditions de location
// en fonction de la sélection des boutons radio "Oui" ou "Non".
// Il rend également la case à cocher "J'accepte les conditions de location" obligatoire lorsque le conteneur est affiché.
document.addEventListener("DOMContentLoaded", () => {
  // 1. Sélection des éléments
  const radioOui = document.getElementById("loc_oui");
  const radioNon = document.getElementById("loc_non");
  const container = document.getElementById("conditions-container");
  const checkbox = document.getElementById("accept_conditions");

  // 2. Fonction de logique
  const toggle = (show) => {
    if (container) {
      container.style.display = show ? "block" : "none";
    }
    if (checkbox) {
      checkbox.required = show; // Rend la case obligatoire si affichée
    }
  };

  // 3. Initialisation au chargement : pour le retour en arrière
  // On vérifie l'état actuel des radios au chargement de la page
  if (radioOui && radioOui.checked) {
    toggle(true);
  } else {
    toggle(false);
  }

  // 4. Ajout des écouteurs d'événements
  if (radioOui) {
    radioOui.addEventListener("change", () => toggle(true));
  }
  if (radioNon) {
    radioNon.addEventListener("change", () => toggle(false));
  }
});

// gere l'affichage de l'estimation des frais de livraison en fonction de la selection de la ville de livraison
document.addEventListener("DOMContentLoaded", () => {
  const lieuSelect = document.getElementById("lieu_id");
  const affichage = document.getElementById("affichage_frais");

  if (!lieuSelect || !affichage) {
    return;
  }

  const updateFrais = () => {
    const lieuId = lieuSelect.value;

    if (!lieuId) {
      affichage.textContent = "0.00";
      return;
    }

    fetch("index.php?page=ajax-frais-livraison&lieu_id=" + lieuId)
      .then((response) => response.json())
      .then((data) => {
        affichage.textContent = data.frais;
      })
      .catch(() => {
        affichage.textContent = "0.00";
      });
  };

  lieuSelect.addEventListener("change", updateFrais);
  updateFrais();
});
