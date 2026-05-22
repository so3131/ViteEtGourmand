// Gestion des filtres de recherche avec AJAX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const resetBtn = document.getElementById('reset-filters');

    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData).toString();

            fetch('index.php?page=filter&' + params)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('menu-container');
                    container.innerHTML = '';
                    if(data.length === 0) {
                        container.innerHTML = '<p>Aucun résultat.</p>';
                    } else {
                        data.forEach(menu => {
                            container.innerHTML += createMenuCard(menu);
                        });
                    }
                })
                .catch(err => console.error("Erreur: ", err));
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            document.getElementById('filterForm').reset();
            fetch('index.php?page=filter')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('menu-container');
                    container.innerHTML = '';
                    data.forEach(menu => {
                        container.innerHTML += createMenuCard(menu);
                    });
                })
                .catch(err => console.error("Erreur: ", err));
        });
    }

    function createMenuCard(menu) {
        return `
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">${menu.titre}</h5>
                        <p class="card-text">${menu.description_menu}</p>
                        <p><strong>Prix:</strong> ${menu.prix_par_personne}€ / pers.</p>
                        <p><strong>Minimum:</strong> ${menu.nombre_personne_minimum} personnes</p>
                    </div>
                    <div class="card-footer">
                        <a href="index.php?page=details-menu&menu_id=${menu.menu_id}" class="btn btn-primary w-100">
                            Voir détails
                        </a>
                    </div>
                </div>
            </div>
        `;
    }
});
