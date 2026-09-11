// 1. Gestion de la soumission du formulaire (Enregistrement)
document
  .getElementById("updateOrderForm")
  .addEventListener("submit", async (e) => {
    e.preventDefault();
    const form = e.target;

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    const checkbox = form.querySelector('[name="pret_materiel"]');
    data.pret_materiel = checkbox ? (checkbox.checked ? 1 : 0) : 0;

    const commandeId = form.dataset.id;

    try {
      // Adaptation de la route avec tes paramètres d'action
      const response = await fetch(
        `index.php?page=update-order-common&commande_id=${commandeId}`,
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        },
      );

      const result = await response.json();
      if (result.success) {
        showToast("Commande mise à jour avec succès !", "success");
        window.location.href = "?page=order-management";
      } else {
        showToast(
          "Erreur : " + (result.message || "Mise à jour échouée."),
          "error",
        );
      }
    } catch (err) {
      console.error("Détail de l'erreur :", err);
      showToast(
        "Erreur technique lors de la communication avec le serveur.",
        "error",
      );
    }
  });

// 2. Gestion de l'autocomplétion de l'adresse
const inputAdresse = document.getElementById("adresse_livraison");
const divSuggestions = document.getElementById("suggestions-adresse");

if (inputAdresse && divSuggestions) {
  inputAdresse.addEventListener("input", () => {
    document.getElementById("ville").value = "";
    document.getElementById("code_postal").value = "";
    document.getElementById("lat").value = "";
    document.getElementById("lon").value = "";
  });
  inputAdresse.addEventListener("input", async (e) => {
    const query = e.target.value.trim();
    if (query.length < 3) {
      divSuggestions.style.display = "none";
      return;
    }

    try {
      const res = await fetch(
        `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5`,
      );
      const data = res.ok ? await res.json() : {};

      divSuggestions.innerHTML = "";
      if (data.features && data.features.length > 0) {
        data.features.forEach((feature) => {
          const item = document.createElement("a");
          item.href = "#";
          item.className = "list-group-item list-group-item-action";
          item.innerText = feature.properties.label;

          item.addEventListener("click", (ev) => {
            ev.preventDefault();
            inputAdresse.value = feature.properties.name;

            const villeEl = document.getElementById("ville");
            if (villeEl) villeEl.value = feature.properties.city || "";

            const cpEl = document.getElementById("code_postal");
            if (cpEl) cpEl.value = feature.properties.postcode || "";

            const latEl =
              document.getElementById("lat") ||
              document.getElementById("latitude");
            if (latEl) latEl.value = feature.geometry.coordinates[1];

            const lonEl =
              document.getElementById("lon") ||
              document.getElementById("longitude");
            if (lonEl) lonEl.value = feature.geometry.coordinates[0];

            divSuggestions.style.display = "none";

            inputAdresse.dispatchEvent(new Event("change", { bubbles: true }));
          });

          divSuggestions.appendChild(item);
        });
        divSuggestions.style.display = "block";
      } else {
        divSuggestions.style.display = "none";
      }
    } catch (err) {
      console.error("Erreur autocomplétion adresse :", err);
    }
  });

  document.addEventListener("click", (e) => {
    if (
      !inputAdresse.contains(e.target) &&
      !divSuggestions.contains(e.target)
    ) {
      divSuggestions.style.display = "none";
    }
  });
}

// 3. Gestion du recalcul du prix total en temps réel
let recalcTimer = null;

async function recalculerPrix() {
  const form = document.getElementById("updateOrderForm");
  if (!form) return;

  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());

  const checkbox = form.querySelector('[name="pret_materiel"]');
  data.pret_materiel = checkbox ? (checkbox.checked ? 1 : 0) : 0;

  try {
    const commandeId = form.dataset.id;
    const response = await fetch(
      `index.php?page=recalculer-prix-common&commande_id=${commandeId}`,
      {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      },
    );

    if (!response.ok) return;
    const result = await response.json();

    if (result.nouveau_prix !== undefined) {
      const totalDisplay = document.getElementById("total-display");
      if (totalDisplay) totalDisplay.innerText = result.nouveau_prix + " €";
    }

    if (result.frais_livraison !== undefined) {
      const fraisDisplay =
        document.getElementById("montant-frais") ||
        document.getElementById("frais-livraison-display");
      if (fraisDisplay) fraisDisplay.innerText = result.frais_livraison + " €";
    }
  } catch (err) {
    console.error("Détail de l'erreur :", err);
    showToast("Erreur technique : " + err.message, "error");
  }
}

document.querySelectorAll("input, select").forEach((el) => {
  ["input", "change"].forEach((eventType) => {
    el.addEventListener(eventType, () => {
      clearTimeout(recalcTimer);
      recalcTimer = setTimeout(recalculerPrix, 400);
    });
  });
});
