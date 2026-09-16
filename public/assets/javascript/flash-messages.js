// =====================================================
// Règle générale : afficher un message (type alerte) depuis n'importe quel JS

// Utilisation : afficherFlashJS("message") ou afficherFlashJS("message", "success" | "info" | "warning" | "danger")
// =====================================================
function afficherFlashJS(message, type = "danger") {
    // 1. Créer le conteneur unique s'il n'existe pas (au sommet du body)
    if (!document.getElementById("flash-js")) {
        const conteneur = document.createElement("div");
        conteneur.id = "flash-js";
        document.body.insertAdjacentElement("afterbegin", conteneur);
    }

    // 2. Créer une alerte Bootstrap dismissible
    const alerte = document.createElement("div");
    alerte.className = "alert alert-" + type + " alert-dismissible fade show";
    alerte.setAttribute("role", "alert");
    alerte.textContent = message;

    const bouton = document.createElement("button");
    bouton.type = "button";
    bouton.className = "btn-close";
    bouton.setAttribute("data-bs-dismiss", "alert");
    bouton.setAttribute("aria-label", "Fermer");
    alerte.appendChild(bouton);

    document.getElementById("flash-js").appendChild(alerte);

    // 3. Auto-fermeture après 5 secondes
    setTimeout(() => alerte.remove(), 5000);
}
