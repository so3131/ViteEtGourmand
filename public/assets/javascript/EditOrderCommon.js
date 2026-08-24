document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('change', async () => {
        const form = document.getElementById('updateOrderForm');
        if (!form) return;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
   
        const checkboxMateriel = form.querySelector('[name="pret_materiel"]');
        if (checkboxMateriel) {
            data.pret_materiel = checkboxMateriel.checked ? 1 : 0;
        }

        try {
            
            const response = await fetch('index.php?page=recalculer-prix', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            // Affiche le nouveau montant
            const totalDisplay = document.getElementById('total-display');
            if (totalDisplay && result.nouveau_prix) {
                totalDisplay.innerText = result.nouveau_prix + ' €';
            }
        } catch (err) {
            console.error("Erreur lors du calcul du prix :", err);
        }
    });
});