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
// j'ecoute le submit des filtres et je bloque le rechargement de la page
// On attend que tout le HTML soit chargé dans le DOM
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("filterForm");
  const resetBtn = document.getElementById("reset-filters");

  if (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();
      console.log("Formulaire détecté et soumis !");

      const formData = new FormData(this);
      for (let [key, value] of formData.entries()) {
        console.log(key, value);
      }
      const params = new URLSearchParams(formData).toString();
      console.log(
        "URL finale envoyée au serveur :",
        "index.php?page=filter&" + params,
      );
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
    console.log("Page sans formulaire de filtre, fonctionnement normal.");
  }

  // Bouton réinitialiser les filtres
  if (resetBtn) {
    resetBtn.addEventListener("click", function () {
      console.log("DEBUG: Bouton reset cliqué !");

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

  // créer de façon dynamique les cartes de menus à partir des données récupérées de la base de données
  function createMenuCard(menu) {
    console.log("ID du menu :", menu.menu_id);
    const menuId = menu.menu_id || "#";
    const detailUrl = `index.php?page=details-menu&menu_id=${menu.menu_id}`;
    return `
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <img src="assets/images/menu_default.jpg" class="card-img-top" alt="${menu.titre}">
                    <div class="card-body">
                        <h5 class="card-title">${menu.titre}</h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            ${menu.vg_theme?.libelle || "Thème inconnu"} - ${menu.regime || "Classique"}
                        </h6>
                        <p class="card-text text-muted small">${menu.description_menu || ""}</p>
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
