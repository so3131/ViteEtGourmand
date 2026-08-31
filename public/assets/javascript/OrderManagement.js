// DataTables French translation
$(document).ready(function() {
       var frLanguage = {
        "emptyTable": "Aucune donnée disponible dans le tableau",
        "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
        "infoEmpty": "Affichage de 0 à 0 sur 0 entrées",
        "infoFiltered": "(filtré à partir de _MAX_ entrées au total)",
        "lengthMenu": "Afficher _MENU_Resultats",
        "loadingRecords": "Chargement...",
        "search": "Rechercher :",
        "zeroRecords": "Aucun résultat trouvé",
        "paginate": {
            "first": "Premier",
            "last": "Dernier",
            "next": "Suivant",
            "previous": "Précédent"
        }
    };

 // Vérifie si la table existe avant d'initialiser
    if ($('#ordersTable').length > 0) {
        $('#ordersTable').DataTable({
            "paging": true,
            "searching": false,
            "ordering": true,
            "info": true,
            "language": frLanguage,
            "columnDefs": [
                { "targets": "_all", "defaultContent": "" }
            ]
        });
        $('.modal').removeClass('d-none').modal('hide');
    }

      if ($('#menusTable').length > 0) {
        $('#menusTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "language": frLanguage
        });
    }
});