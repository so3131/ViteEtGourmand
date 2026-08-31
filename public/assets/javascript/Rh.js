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
    $('#employesTable').DataTable({
        "renderer": "bootstrap", 
        "language": frLanguage,
        "pageLength": 10,
        "ordering": true,
        "searching": true
    });
});