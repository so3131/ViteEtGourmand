$(document).ready(function () {
  const frLanguage = {
    emptyTable: "Aucune donnée disponible",
    info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
    infoEmpty: "Affichage de 0 à 0 sur 0 entrées",
    infoFiltered: "(filtré à partir de _MAX_ entrées au total)",
    lengthMenu: "Afficher _MENU_ résultats",
    loadingRecords: "Chargement...",
    search: "Rechercher",
    zeroRecords: "Aucun résultat trouvé",
    paginate: {
      first: "Premier",
      last: "Dernier",
      next: "Suivant",
      previous: "Précédent",
    },
  };

  if ($("#ordersTable").length) {
    $("#ordersTable").DataTable({
      responsive: true,
      columnDefs: [
        // 1. Valeur par défaut pour éviter les erreurs de données manquantes
        { targets: "_all", defaultContent: "" },

        // 2. Colonnes toujours visibles, même sur mobile
        { responsivePriority: 1, targets: 0 }, // Client — identifie la ligne
        { responsivePriority: 2, targets: 5 }, // Statut — vue rapide de l'état

        // 3. Colonnes visibles uniquement à partir du breakpoint desktop
        { targets: -1, className: "desktop" }, // Actions (select + Voir/Modif./Annuler) — trop dense pour mobile
        { targets: 6, className: "desktop" }, // Total
        { targets: 7, className: "desktop" }, // Date Limite Restitution
        { targets: 4, className: "desktop" }, // Prestation
      ],
      paging: true,
      searching: false,
      ordering: true,
      info: true,
      lengthChange: true,
      dom: '<"dt-toolbar d-flex justify-content-between align-items-center mb-3"lf>rtip',
      language: frLanguage,
    });
  }

  if ($("#menusTable").length) {
    $("#menusTable").DataTable({
      responsive: true,
      columnDefs: [
        { responsivePriority: 1, targets: 0 }, // Menu —
        { responsivePriority: 2, targets: 1 }, // Prix/Pers —
        { targets: -1, className: "desktop" }, // Actions
        { targets: 2, className: "desktop" }, // Plats inclus
      ],
      paging: true,
      searching: true,
      ordering: true,
      dom: '<"dt-toolbar d-flex justify-content-between align-items-center mb-3"lf>rtip',
      info: true,
      language: frLanguage,
    });
  }

  if ($("#employesTable").length) {
    $("#employesTable").DataTable({
      responsive: true,
      columnDefs: [
        { responsivePriority: 1, targets: 0 }, // Employé
        { responsivePriority: 3, targets: 3 }, // Statut
        { targets: -1, className: "desktop" }, // Actions
        { targets: 2, className: "desktop" }, // Arrivée
      ],
      renderer: "bootstrap",
      paging: true,
      searching: true,
      ordering: true,
      dom: '<"dt-toolbar d-flex justify-content-between align-items-center mb-3"lf>rtip',
      pageLength: 10,
      language: frLanguage,
    });
  }

  if ($("#platsTable").length) {
    $("#platsTable").DataTable({
      responsive: true,
      columnDefs: [
        { responsivePriority: 1, targets: 0 }, // Nom du plat — identifie la ligne
        { responsivePriority: 2, targets: 4 }, // Actions
        { responsivePriority: 10002, targets: 1, className: "desktop" }, // Catégorie
        { responsivePriority: 10001, targets: 2, className: "desktop" }, // Description
        { responsivePriority: 10003, targets: 3, className: "desktop" }, // statut
      ],
      paging: true,
      searching: true,
      ordering: true,
      dom: '<"dt-toolbar d-flex justify-content-between align-items-center mb-3"lf>rtip',
      pageLength: 10,
      language: frLanguage,
    });
  }

 if ($("#moderationTable").length) {
  $("#moderationTable").DataTable({
    responsive: true,
    columnDefs: [
      { targets: 0, className: "all" },   // Utilisateur — toujours visible
      { targets: 2, className: "all" },   // Rôle — toujours visible
      { targets: 1, className: "none" },  // Email 
      { targets: -1, className: "none" }, // Actions (Désactiver/Réactiver/Protégé) toujours dans le "+"
    ],
    paging: true,
    searching: false,
    ordering: true,
    dom: '<"dt-toolbar d-flex justify-content-between align-items-center mb-3"lf>rtip',
    language: frLanguage,
  });
}

  // Nettoyage ARIA
  setTimeout(function () {
    document
      .querySelectorAll(".dataTables_paginate a, .dataTables_paginate span")
      .forEach(function (el) {
        el.removeAttribute("aria-controls");
        el.removeAttribute("aria-disabled");
        el.removeAttribute("role");
      });
  }, 100);
});

//`dom` par défaut de DataTables = `'lfrtip'`

// l = longueur (en haut à gauche)
// f = recherche (en haut à droite)
// r = indicateur de traitement
// t = tableau
// i = infos (en bas à gauche)
// p = pagination (en bas à droite)

// responsivePriority : contrôle quelle colonne disparaît en premier sur petit écran

// Plus le chiffre est PETIT, plus la colonne résiste (reste visible longtemps)
// Plus le chiffre est GRAND, plus la colonne disparaît tôt (cachée dans le "+")

// targets: 0    → 1ère colonne (index qui commence à 0, pas à 1)
// targets: 1    → 2ème colonne
// targets: -1   → dernière colonne (index négatif = compte depuis la droite)
// targets: -2   → avant-dernière colonne

// Valeur par défaut si non précisée : 10000 (comportement "normal", ni prioritaire ni sacrifiée)
// Utiliser 10001+ pour forcer une colonne à disparaître AVANT les autres, même les non-configurées

// Exemple : { responsivePriority: 1, targets: 0 }     → colonne quasi jamais cachée
//           { responsivePriority: 10001, targets: 2 } → colonne cachée en priorité
// all	Toujours visible, peu importe la taille d'écran
// none	Jamais visible dans le tableau, mais la donnée apparaît dans la ligne dépliée (+)
// never	Jamais visible nulle part, même pas dans la ligne dépliée
// desktop / not-mobile	Caché sur mobile, visible sur tablette/desktop
// min-tablet, min-phone-l, etc.	Contrôle fin par breakpoint précis
