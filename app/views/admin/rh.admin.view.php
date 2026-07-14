<div class="admin-section">
    <div class="section-header">
        <h2><i class="fa-solid fa-user-plus"></i> Créer un nouveau compte employé</h2>
    </div>

    <form action="traitement_rh.php" method="POST" class="form-container">
        <div class="form-grid">
            <div class="form-group">
                <label for="emp_nom">Nom</label>
                <input type="text" id="emp_nom" name="nom" placeholder="Ex: Dupont" required>
            </div>

            <div class="form-group">
                <label for="emp_prenom">Prénom</label>
                <input type="text" id="emp_prenom" name="prenom" placeholder="Ex: Jean" required>
            </div>

            <div class="form-group">
                <label for="emp_email">Adresse Email</label>
                <input type="email" id="emp_email" name="email" placeholder="jean.dupont@ecoride.fr" required>
            </div>

            <div class="form-group">
                <label for="emp_password">Mot de passe provisoire</label>
                <input type="password" id="emp_password" name="password" required>
                <small>L'employé devra le modifier à sa première connexion.</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-moderation btn-valider">
                <i class="fa-solid fa-floppy-disk"></i> Créer le compte
            </button>
            <button type="reset" class="btn-moderation btn-refuser">
                <i class="fa-solid fa-xmark"></i> Annuler
            </button>
        </div>
    </form>
</div>

<div class="admin-section mt-5">
    <div class="section-header">
        <h2><i class="fa-solid fa-users"></i> Équipe actuelle</h2>
    </div>

    <div class="table-container">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Employé</th>
                    <th>Email</th>
                    <th>Date d'arrivée</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">JD</div>
                            <strong>Jean Dupont</strong>
                        </div>
                    </td>
                    <td>j.dupont@ecoride.fr</td>
                    <td>12/01/2026</td>
                    <td><span class="badge badge-active">Actif</span></td>
                    <td>
                        <button class="btn-icon" title="Réinitialiser le MDP"><i class="fa-solid fa-key"></i></button>
                        <button class="btn-icon btn-danger" title="Supprimer"><i class="fa-solid fa-user-slash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">MA</div>
                            <strong>Marie Alix</strong>
                        </div>
                    </td>
                    <td>m.alix@ecoride.fr</td>
                    <td>05/02/2026</td>
                    <td><span class="badge badge-active">Actif</span></td>
                    <td>
                        <button class="btn-icon" title="Réinitialiser le MDP"><i class="fa-solid fa-key"></i></button>
                        <button class="btn-icon btn-danger" title="Supprimer"><i class="fa-solid fa-user-slash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>