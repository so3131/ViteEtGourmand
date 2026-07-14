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