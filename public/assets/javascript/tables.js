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
  paging: true,
  searching: false,
  ordering: true,
  info: true,
  lengthChange: true,
  dom: '<"dt-toolbar d-flex justify-content-between align-items-center mb-3"lf>rtip',
  language: frLanguage,
  columnDefs: [{ targets: "_all", defaultContent: "" }],
});

  }

  if ($("#menusTable").length) {
    $("#menusTable").DataTable({
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
      paging: true,
      searching: true,
      ordering: true,
      dom: '<"dt-toolbar d-flex justify-content-between align-items-center mb-3"lf>rtip',
      pageLength: 10,
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