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
            const response = await fetch('index.php?page=recalculer-prix-common', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const textResponse = await response.text(); // Récupère en texte brut d'abord
            console.log("Réponse brute du serveur :", textResponse); // Pour voir si des warnings s'incrustent

            const result = JSON.parse(textResponse);
            
            const totalDisplay = document.getElementById('total-display');
            if (totalDisplay && result.nouveau_prix) {
                // Formate proprement avec 2 décimales si besoin
                totalDisplay.innerText = parseFloat(result.nouveau_prix).toFixed(2) + ' €';
            }
        } catch (err) {
            console.error("Erreur lors du calcul du prix :", err);
        }
    });
});