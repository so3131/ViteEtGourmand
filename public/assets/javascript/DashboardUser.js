
// Sélectionne tous les boutons d'annulation de commande
document.querySelectorAll('.btn-erase-order').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.preventDefault();
        const commandeId = btn.dataset.commandeId;
        
        if (confirm('Êtes-vous sûr de vouloir annuler cette commande ?')) {
            fetch('index.php?page=erase-order&commande_id=' + commandeId, {
                method: 'POST'
            })
            .then(r => r.json())
            .then(data => {
                alert(data.message);
                location.reload(); 
                // Recharge la page pour voir le changement
            });
        }
    });
});
// Gestion de l'affichage de l'alerte de succès
document.addEventListener("DOMContentLoaded", function() {
    const alertElement = document.getElementById('success-alert');
    
    if (alertElement) {
        // Au bout de 4 secondes (4000 ms)
        setTimeout(function() {
            // 1. Masquer l'alerte en douceur
            alertElement.style.transition = "opacity 0.5s ease";
            alertElement.style.opacity = "0";
            
            setTimeout(function() {
                alertElement.remove();
            }, 500);

            // 2. Nettoyer proprement l'URL (retire juste "success=1" sans casser les autres paramètres)
            const url = new URL(window.location.href);
            url.searchParams.delete('success'); // Supprime le paramètre success
            window.history.replaceState({}, document.title, url.toString());
            
        }, 4000);
    }
});