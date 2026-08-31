// 1. Gestion de la soumission du formulaire (Enregistrement)
document.getElementById('updateOrderForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
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
            window.location.href = '?page=dashboard-user';
        } else {
            alert('Erreur : ' + result.message);
        }
    } catch (err) {
        console.error("Détail de l'erreur :", err); 
        alert('Erreur technique lors de la communication avec le serveur.');
    }
});

// 2. Gestion de l'autocomplétion de l'adresse (API Adresse data.gouv.fr)
const inputAdresse = document.getElementById('adresse_livraison');
const divSuggestions = document.getElementById('suggestions-adresse');

if (inputAdresse) {
    inputAdresse.addEventListener('input', async (e) => {
        const query = e.target.value.trim();
        if (query.length < 3) {
            divSuggestions.style.display = 'none';
            return;
        }

        try {
            const res = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5`);
            const data = res.json ? await res.json() : {};
            
            divSuggestions.innerHTML = '';
            if (data.features && data.features.length > 0) {
                data.features.forEach(feature => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerText = feature.properties.label;
                    
                    item.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        inputAdresse.value = feature.properties.name;
                        document.getElementById('ville').value = feature.properties.city || '';
                        document.getElementById('code_postal').value = feature.properties.postcode || '';
                        document.getElementById('lat').value = feature.geometry.coordinates[1];
                        document.getElementById('lon').value = feature.geometry.coordinates[0];
                        
                        divSuggestions.style.display = 'none';

                        inputAdresse.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                    
                    divSuggestions.appendChild(item);
                });
                divSuggestions.style.display = 'block';
            } else {
                divSuggestions.style.display = 'none';
            }
        } catch (err) {
            console.error("Erreur autocomplétion adresse :", err);
        }
    });

    document.addEventListener('click', (e) => {
        if (!inputAdresse.contains(e.target) && !divSuggestions.contains(e.target)) {
            divSuggestions.style.display = 'none';
        }
    });
}

// 3. Gestion du recalcul du prix total en temps réel
document.querySelectorAll('input, select').forEach(el => {
    ['input', 'change'].forEach(eventType => {
        el.addEventListener(eventType, async () => {
            const form = document.getElementById('updateOrderForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            data.pret_materiel = form.querySelector('[name="pret_materiel"]').checked ? 1 : 0;

            try {
                const commandeId = document.getElementById('updateOrderForm').dataset.id;
                const response = await fetch(`index.php?page=recalculer-prix&commande_id=${commandeId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                console.log("Réponse reçue du serveur :", result);
                
                // 1. Mise à jour du prix total si présent
                if (result.nouveau_prix !== undefined) {
                    document.getElementById('total-display').innerText = result.nouveau_prix + ' €';
                }
                
                // 2. Mise à jour des frais de livraison si présents
                if (result.frais_livraison !== undefined) {
                    document.getElementById('montant-frais').innerText = result.frais_livraison;
                }
                
                // 3. Gestion des erreurs serveur éventuelles
                if (result.error) {
                    console.error("Erreur serveur recalcul :", result.error);
                }
       
            } catch (err) {
        console.error("Détail complet de l'erreur fetch :", err); 
        console.error("Message :", err.message);
        alert('Erreur technique : ' + err.message);
    }
        });
    });
});
