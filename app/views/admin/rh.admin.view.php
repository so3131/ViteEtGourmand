<!-- Bloc 1 : Formulaire d'ajout d'employé -->
    <div class="card shadow-sm p-4">
        <div class="section-header mb-3">
            <h2><i class="fa-solid fa-user-plus"></i> Ajouter un employé</h2>
        </div>

       <form action="index.php?page=rh-admin-create" method="POST" autocomplete="off">
            <!-- Champ Email -->
            <?php render_form_input('email', 'Email de l\'employé (Username)', 'email', 'Entrez l\'email...', ''); ?>

            <!-- Champ Mot de passe -->
            <?php render_form_input('password', 'Mot de passe temporaire', 'password', 'Entrez le mot de passe...', ''); ?>

            <button type="submit" class="btn btn-primary mt-2">Créer l'employé</button>
        </form>
    </div>


<!-- Bloc 2 : Liste de l'équipe actuelle -->
    <div class="card shadow-sm p-4">
        <div class="section-header mb-3">
            <h2><i class="fa-solid fa-users"></i> Équipe actuelle</h2>
        </div>

        <div class="table-container">
<table class="table-admin table align-middle" id="employesTable">
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
                    <?php if (!empty($employes)): ?>
                        <?php foreach ($employes as $emp): ?>
                            <?php 
                                // Génération des initiales pour l'avatar (ex: Jean Dupont -> JD)
                                $emailParts = explode('@', $emp['email']);
                                $initiales = strtoupper(substr($emailParts[0], 0, 2));
                            ?>
                            <tr>
                                <td>
                                    <div class="user-info d-flex align-items-center gap-2">
                                        <div class="user-avatar"><?= $initiales ?></div>
                                        <strong><?= htmlspecialchars($emailParts[0]) ?></strong>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($emp['email']) ?></td>
                                <td><?= isset($emp['created_at']) ? date('d/m/Y', strtotime($emp['created_at'])) : 'N/C' ?></td>
                                <td>
    <?php if (isset($emp['est_actif']) && $emp['est_actif'] == 1): ?>
        <span class="badge bg-success">Actif</span>
    <?php else: ?>
        <span class="badge bg-danger">Inactif</span>
    <?php endif; ?>
</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-secondary btn-icon" title="Réinitialiser le MDP"><i class="fa-solid fa-key"></i></button>
  <!-- Bouton Dynamique Activer / Désactiver -->
    <?php if (isset($emp['est_actif']) && $emp['est_actif'] == 1): ?>
        <!-- S'il est actif, on propose de le désactiver (rouge/cadenas) -->
        <a href="index.php?page=rh-admin-toggle&id=<?= $emp['utilisateur_id'] ?>" 
           class="btn btn-sm btn-outline-danger btn-icon" 
           title="Désactiver le compte" 
           onclick="return confirm('Voulez-vous vraiment désactiver cet employé ? Il ne pourra plus se connecter.');">
           <i class="fa-solid fa-user-lock"></i>
        </a>
    <?php else: ?>
        <!-- S'il est inactif, on propose de le réactiver (vert/utilisateur) -->
        <a href="index.php?page=rh-admin-toggle&id=<?= $emp['utilisateur_id'] ?>" 
           class="btn btn-sm btn-outline-success btn-icon" 
           title="Réactiver le compte" 
           onclick="return confirm('Voulez-vous réactiver cet employé ?');">
           <i class="fa-solid fa-user-check"></i>
        </a>
    <?php endif; ?>
                                    <a href="index.php?page=rh-admin-delete&id=<?= $emp['utilisateur_id'] ?>" 
   class="btn btn-sm btn-outline-danger btn-icon" 
   title="Supprimer" 
   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet employé ?');">
   <i class="fa-solid fa-user-slash"></i>
</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">Aucun employé enregistré pour le moment.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
