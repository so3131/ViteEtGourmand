// gestion du slider de prix
document.addEventListener("DOMContentLoaded", function () {
  // 1. Déclaration des éléments du DOM
  const slider = document.getElementById("price-slider");
  const priceSelect = document.getElementById("price-max-select");
  const minDisplay = document.getElementById("slider-min");
  const maxDisplay = document.getElementById("slider-max");

  if (slider) {
    noUiSlider.create(slider, {
      start: [0, 132],
      connect: true,
      range: { min: 0, max: 132 },
      format: {
        to: (value) => Math.round(value),
        from: (value) => Math.round(value),
      },
    });

    // Ajouter des attributs ARIA pour l'accessibilité
    const handles = slider.querySelectorAll(".noUi-handle");
    if (handles[0]) handles[0].setAttribute("aria-label", "Prix minimum");
    if (handles[1]) handles[1].setAttribute("aria-label", "Prix maximum");
    // 2. Fonction pour mettre à jour la plage du slider
    function updateSliderRange(max) {
      slider.noUiSlider.updateOptions({
        range: { min: 0, max: max },
      });
    }

    slider.noUiSlider.on("update", function (values) {
      minDisplay.textContent = Math.round(values[0]) + "€";
      maxDisplay.textContent = Math.round(values[1]) + "€";

      document.getElementById("input-slider-min").value = Math.round(values[0]);
      document.getElementById("input-slider-max").value = Math.round(values[1]);
    });

    priceSelect.addEventListener("change", function () {
      const selectedMax = parseInt(this.value) || 132;
      updateSliderRange(selectedMax);
      slider.noUiSlider.set([0, selectedMax]);
      document.getElementById("input-slider-min").value =
        slider.noUiSlider.get()[0];
      document.getElementById("input-slider-max").value =
        slider.noUiSlider.get()[1];
    });
  }
});

// gestion de filtres
// ecoute le submit des filtres et bloque le rechargement de la page
// attend la réponse du serveur et affiche les résultats dans le DOM
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("filterForm");
  const resetBtn = document.getElementById("reset-filters");

  if (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();

      const formData = new FormData(this);

      const params = new URLSearchParams(formData).toString();

      fetch("index.php?page=filter&" + params)
        .then((response) => response.json())
        .then((data) => {
          const container = document.getElementById("menu-container");
          container.innerHTML = "";

          if (data.length === 0) {
            container.innerHTML = "<p>Aucun résultat.</p>";
          } else {
            data.forEach((menu) => {
              container.innerHTML += createMenuCard(menu);
            });
          }
        })
        .catch((err) => console.error("Erreur de fetch : ", err));
    });
  } else {
    // Formulaire de filtre absent sur cette page.
  }

  // Bouton réinitialiser les filtres
  if (resetBtn) {
    resetBtn.addEventListener("click", function () {
      // Réinitialiser le formulaire
      document.getElementById("filterForm").reset();

      // Fetch SANS paramètres pour récupérer tous les menus
      fetch("index.php?page=filter")
        .then((response) => response.json())
        .then((data) => {
          const container = document.getElementById("menu-container");
          container.innerHTML = "";
          if (data.length === 0) {
            container.innerHTML = "<p>Aucun résultat.</p>";
          } else {
            data.forEach((menu) => {
              container.innerHTML += createMenuCard(menu);
            });
          }
        })
        .catch((err) => console.error("Erreur de fetch : ", err));
    });
  }
  // Fonction pour échapper les caractères HTML
  function escapeHtml(value) {
    const div = document.createElement("div");
    div.textContent = value ?? "";
    return div.innerHTML;
  }
  // Fonction pour obtenir la photo principale d'un menu
  function getMenuPhoto(menu) {
    const plats = menu.plats_structures || {};

    const platPrincipal =
      plats.Plat || plats["Entrée"] || plats.Dessert || null;

    return platPrincipal && platPrincipal.photo
      ? platPrincipal.photo
      : "assets/img/plats/default.webp";
  }

  // créer de façon dynamique les cartes de menus à partir des données récupérées de la base de données
 function createMenuCard(menu) {
    const menuId = menu.menu_id || "#";
    const detailUrl = `index.php?page=details-menu&menu_id=${menu.menu_id}`;
    const photoUrl = getMenuPhoto(menu);

    return `
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="${escapeHtml(photoUrl)}"
             class="card-img-top" alt="${escapeHtml(menu.titre)}" style="height: 300px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">${escapeHtml(menu.titre)}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            ${escapeHtml(menu.vg_theme?.libelle || "Thème inconnu")} - ${escapeHtml(menu.regime || "Classique")}
                        </h6>
                        <p class="card-text text-muted small">${escapeHtml(menu.description_menu || "")}</p>
                        <ul class="list-unstyled small">
                            <li><strong>Minimum :</strong> ${menu.nombre_personne_minimum} personnes</li>
                            <li><strong>Prix :</strong> ${parseFloat(menu.prix_par_personne).toFixed(2)} € / pers.</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="${detailUrl}" class="btn btn-primary w-100">
                            Voir le détail
                        </a>
                    </div>
                </div>
            </div>
        `;
  }
});
