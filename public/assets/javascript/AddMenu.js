function addNewPlat(categorie) {
  const titre = document.getElementById("titrePlat" + categorie).value.trim();
  const description = document
    .getElementById("descPlat" + categorie)
    .value.trim();
  const photoInput = document.getElementById("photoPlat" + categorie);

  if (!titre) {
    showToast("Le titre du plat est obligatoire.", "error");
    return;
  }

  const formData = new FormData();
  const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;

  if (csrfToken) {
    formData.append("csrf_token", csrfToken);
  }
  formData.append("titre_plat", titre);
  formData.append("description_plat", description);
  formData.append("categorie", categorie);

  if (photoInput.files[0]) {
    formData.append("photo", photoInput.files[0]);
  }

  const formContainer = document.getElementById("addPlatForm" + categorie);
  const checkedAllergenes = formContainer.querySelectorAll(
    'input[name="allergenes[]"]:checked',
  );
  checkedAllergenes.forEach((cb) => {
    formData.append("allergenes[]", cb.value);
  });

  fetch("index.php?page=create-plat-ajax", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        const container = document.getElementById("platsContainer" + categorie);

        // Supprimer le message "Aucun plat" s'il était présent
        const emptyMsg = container.querySelector(".text-muted");
        if (emptyMsg) {
          emptyMsg.remove();
        }

        const colDiv = document.createElement("div");
        colDiv.className = "col-md-6 mb-2";
        colDiv.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="plats[]" value="${data.plat_id}" id="modalPlat${data.plat_id}" checked>
                    <label class="form-check-label" for="modalPlat${data.plat_id}">
                        ${escapeHtml(titre)}
                    </label>
                </div>
            `;
        container.appendChild(colDiv);

        document.getElementById("titrePlat" + categorie).value = "";
        document.getElementById("descPlat" + categorie).value = "";
        photoInput.value = "";
        formContainer
          .querySelectorAll('input[name="allergenes[]"]')
          .forEach((cb) => (cb.checked = false));

        const collapseElement = document.getElementById(
          "addPlatForm" + categorie,
        );
        const bsCollapse =
          bootstrap.Collapse.getInstance(collapseElement) ||
          new bootstrap.Collapse(collapseElement);
        bsCollapse.hide();

        showToast("Plat ajouté avec succès.");
      } else {
        showToast("Erreur : " + (data.message || "Erreur inconnue"), "error");
      }
    })
    .catch((error) => {
      console.error("Erreur:", error);
      showToast("Erreur technique : " + error.message, "error");
    });
}

// éviter les failles XSS lors de l'affichage du titre
function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, function (m) {
    return map[m];
  });
}
