// ==========================================
// GESTION GLOBALE DE LA PAGE DE COMMANDE
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
  // ------------------------------------------
  // 1. GESTION DES CONDITIONS DE LOCATION
  // ------------------------------------------
  const radioOui = document.getElementById("loc_oui");
  const radioNon = document.getElementById("loc_non");
  const container = document.getElementById("conditions-container");
  const checkbox = document.getElementById("accept_conditions");

  const toggle = (show) => {
    if (container) {
      container.style.display = show ? "block" : "none";
    }
    if (checkbox) {
      checkbox.required = show; // Rend la case obligatoire si affichée
    }
  };

  if (radioOui && radioOui.checked) {
    toggle(true);
  } else {
    toggle(false);
  }

  if (radioOui) {
    radioOui.addEventListener("change", () => toggle(true));
  }
  if (radioNon) {
    radioNon.addEventListener("change", () => toggle(false));
  }

  // ------------------------------------------
  // 2. API ADRESSE & FRAIS DE LIVRAISON
  // ------------------------------------------
  const inputAdresse = document.getElementById("adresse_livraison");
  const divSuggestions = document.getElementById("suggestions-adresse");
  const inputVille = document.getElementById("ville");
  const inputLat = document.getElementById("lat");
  const inputLon = document.getElementById("lon");

  const divInfoLivraison = document.getElementById("info-livraison");
  const spanMontantFrais = document.getElementById("montant-frais");

  let timeoutId = null;

  // Fonction pour interroger l'API de calcul des frais côté PHP
  function calculerFrais(ville, lat, lon) {
    if (!ville || !lat || !lon) return;

    fetch(
      `index.php?page=ajax-frais-livraison&ville=${encodeURIComponent(ville)}&lat=${lat}&lon=${lon}`,
    )
      .then((response) => response.json())
      .then((data) => {
        if (spanMontantFrais && divInfoLivraison) {
          spanMontantFrais.textContent = data.frais;
          divInfoLivraison.style.display = "block";
        }
      })
      .catch((error) => console.error("Erreur calcul frais:", error));
  }

  // Calcul automatique au chargement si les champs sont déjà pré-remplis (ex: retour en arrière)
  if (
    inputVille &&
    inputVille.value &&
    inputLat &&
    inputLat.value &&
    inputLon &&
    inputLon.value
  ) {
    calculerFrais(inputVille.value, inputLat.value, inputLon.value);
  }

  // Écouteur de frappe pour l'autocomplétion
  if (inputAdresse) {
    inputAdresse.addEventListener("input", function () {
      const query = this.value.trim();
      clearTimeout(timeoutId);
      const inputCp = document.getElementById("code_postal");

      // SÉCURITÉ : On vide immédiatement les valeurs pour éviter de garder l'ancienne adresse
      if (inputVille) inputVille.value = "";
      if (inputLat) inputLat.value = "";
      if (inputLon) inputLon.value = "";
      if (inputCp) inputCp.value = "";

      // Cacher les frais affichés tant qu'une nouvelle adresse n'est pas sélectionnée
      if (divInfoLivraison) divInfoLivraison.style.display = "none";

      if (query.length < 3) {
        if (divSuggestions) {
          divSuggestions.style.display = "none";
          divSuggestions.innerHTML = "";
        }
        return;
      }

      timeoutId = setTimeout(() => {
        fetch(
          `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5&type=housenumber`,
        )
          .then((response) => response.json())
          .then((data) => {
            if (!divSuggestions) return;
            divSuggestions.innerHTML = "";
            if (data.features && data.features.length > 0) {
              data.features.forEach((feature) => {
                const item = document.createElement("a");
                item.href = "#";
                item.classList.add("list-group-item", "list-group-item-action");
                item.textContent = feature.properties.label;

                item.addEventListener("click", function (e) {
                  e.preventDefault();
                  if (feature.properties.type !== "housenumber") {
                    afficherFlashJS(
                      "Veuillez choisir une adresse avec numéro de rue (ex : 12 rue des Lilas).",
                    );
                    return;
                  }
                  inputAdresse.value = feature.properties.label;
                  inputVille.value = feature.properties.city || "";

                  document.getElementById("code_postal").value =
                    feature.properties.postcode || "";

                  // Mise à jour des coordonnées exactes
                  inputLon.value = feature.geometry.coordinates[0];
                  inputLat.value = feature.geometry.coordinates[1];

                  divSuggestions.style.display = "none";
                  divSuggestions.innerHTML = "";

                  // Déclenchement immédiat du calcul des frais
                  calculerFrais(
                    inputVille.value,
                    inputLat.value,
                    inputLon.value,
                  );
                });

                divSuggestions.appendChild(item);
              });
              divSuggestions.style.display = "block";
            } else {
              divSuggestions.style.display = "none";
            }
          })
          .catch((err) => console.error("Erreur API Adresse:", err));
      }, 300);
    });
  }

  // Cacher les suggestions si l'utilisateur clique ailleurs sur la page
  document.addEventListener("click", function (e) {
    if (
      inputAdresse &&
      divSuggestions &&
      !inputAdresse.contains(e.target) &&
      !divSuggestions.contains(e.target)
    ) {
      divSuggestions.style.display = "none";
    }
  });
  const formLivraison = document.getElementById("form-livraison");
  if (formLivraison) {
    formLivraison.addEventListener("submit", (e) => {
      const ville = document.getElementById("ville");
      const lat = document.getElementById("lat");
      const lon = document.getElementById("lon");
      if (!ville || !ville.value || !lat || !lon || !lat.value || !lon.value) {
        e.preventDefault();
        afficherFlashJS(
          "Veuillez sélectionner une adresse complète dans les suggestions.",
        );
      }
    });
    const cp = document.getElementById("code_postal");
    if (
      !ville ||
      !ville.value ||
      !cp ||
      !cp.value ||
      !lat ||
      !lon ||
      !lat.value ||
      !lon.value
    ) {
      e.preventDefault();
      afficherFlashJS(
        "Veuillez sélectionner une adresse complète dans les suggestions.",
      );
    }
  }
});
