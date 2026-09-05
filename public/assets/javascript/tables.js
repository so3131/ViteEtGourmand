// =====================================================================
// Configuration globale de DataTables
// ====================================================================

$(document).ready(function() {

    // 1. Traduction française
    const frLanguage = {
        "emptyTable": "Aucune donnée disponible dans le tableau",
        "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
        "infoEmpty": "Affichage de 0 à 0 sur 0 entrées",
        "infoFiltered": "(filtré à partir de _MAX_ entrées au total)",
        "lengthMenu": "Afficher _MENU_ résultats",
        "loadingRecords": "Chargement...",
        "search": "Rechercher",
        "zeroRecords": "Aucun résultat trouvé",
        "paginate": {
            "first": "Premier",
            "last": "Dernier",
            "next": "Suivant",
            "previous": "Précédent"
        }
    };

    // 2. Table des Commandes (Order Management)
    if ($('#ordersTable').length > 0) {
        $('#ordersTable').DataTable({
            paging: true,
            searching: false,
            ordering: true,
            info: true,
            language: frLanguage,
            columnDefs: [
                { targets: "_all", defaultContent: "" }
            ]
        });
        $('.modal').removeClass('d-none').modal('hide');
    }

    // 3. Table des Menus (Menu Management)
    if ($('#menusTable').length > 0) {
        $('#menusTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            language: frLanguage
        });
    }

    // 4. Table des Employés (RH Admin)
    if ($('#employesTable').length > 0) {
        $('#employesTable').DataTable({
            renderer: "bootstrap",
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 10,
            language: frLanguage
        });
    }

    // 5. Table des Plats (Menu Management)
    if ($('#platsTable').length > 0) {
        $('#platsTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            pageLength: 10,
            language: frLanguage
        });
    }

});