// 1. Gestion de la soumission du formulaire (Enregistrement)
document.getElementById('updateOrderForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    
    // On crée un objet propre à partir du formulaire
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // On force la valeur du checkbox
    data.pret_materiel = form.querySelector('[name="pret_materiel"]').checked ? 1 : 0;
    
    const commandeId = form.dataset.id;

    try {
        const response = await fetch(`index.php?page=update-order&commande_id=${commandeId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            alert('Commande mise à jour avec succès !');
            window.location.href = '?page=dashboard-user'; // Redirection vers la page de menu ou une autre page pertinente
        } else {
            alert('Erreur : ' + result.message);
        }
    } catch (err) {
    // 1. Affiche l'erreur complète dans la console pour déboguer
    console.error("Détail de l'erreur :", err); 
    
    // 2. Informe l'utilisateur que quelque chose a mal tourné
    alert('Erreur technique lors de la communication avec le serveur.');
}
});

// 2. Calcul en temps réel (AJAX)
document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('change', async () => {
        const form = document.getElementById('updateOrderForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // On ajoute le checkbox manuellement ici aussi pour le calcul
        data.pret_materiel = form.querySelector('[name="pret_materiel"]').checked ? 1 : 0;

        const response = await fetch('index.php?page=recalculer-prix', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        // Affiche le nouveau montant
        document.getElementById('total-display').innerText = result.nouveau_prix + ' €';
    });
});