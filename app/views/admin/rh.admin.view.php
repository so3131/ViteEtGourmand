<?php 
// Messages flash globaux
if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<div class="container-fluid my-4">
    
    <!-- ========================================== -->
    <!-- SECTION 1 : GESTION DES EMPLOYÉS (RH)     -->
    <!-- ========================================== -->
    <div class="admin-section mb-5">
        <div class="section-header mb-4">
            <h2><i class="fa-solid fa-user-shield"></i> Gestion de l'équipe</h2>
            <p class="text-muted">Ajoutez de nouveaux employés et gérez les accès de votre équipe actuelle.</p>
        </div>

        <!-- Bloc 1A : Formulaire d'ajout d'employé -->
        <div class="card shadow-sm p-4 mb-4">
            <div class="section-header mb-3">
                <h4><i class="fa-solid fa-user-plus"></i> Ajouter un employé</h4>
            </div>

            <form action="index.php?page=rh-admin-create" method="POST" autocomplete="off">
                <!-- Champ Email -->
                <?php render_form_input('email', 'Email de l\'employé (Username)', 'email', 'Entrez l\'email...', ''); ?>

                <!-- Champ Mot de passe -->
                <?php render_form_input('password', 'Mot de passe temporaire', 'password', 'Entrez le mot de passe...', ''); ?>

                <button type="submit" class="btn btn-primary mt-2">Créer l'employé</button>
            </form>
        </div>

        <!-- Bloc 1B : Liste de l'équipe actuelle -->
        <div class="card shadow-sm p-4">
            <div class="section-header mb-3">
                <h4><i class="fa-solid fa-users"></i> Équipe actuelle</h4>
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
                                        
                                        <?php if (isset($emp['est_actif']) && $emp['est_actif'] == 1): ?>
                                            <a href="index.php?page=rh-admin-toggle&id=<?= $emp['utilisateur_id'] ?>" 
                                               class="btn btn-sm btn-outline-danger btn-icon" 
                                               title="Désactiver le compte" 
                                               onclick="return confirm('Voulez-vous vraiment désactiver cet employé ?');">
                                               <i class="fa-solid fa-user-lock"></i>
                                            </a>
                                        <?php else: ?>
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
    </div>


    <hr class="my-5">


    <!-- ========================================== -->
    <!-- SECTION 2 : MODÉRATION GLOBALE DES USERS  -->
    <!-- ========================================== -->
    <div class="admin-section">
        <div class="section-header mb-3">
            <h2><i class="fas fa-users-gear"></i> Modération des clients & utilisateurs</h2>
            <p class="text-muted">Recherchez un utilisateur pour gérer ses accès et son statut sur le site.</p>
        </div>

        <!-- Barre de recherche et filtres -->
        <div class="search-bar-container card shadow-sm p-3 mb-4">
            <form class="search-ban-form d-flex gap-2 align-items-center flex-wrap" action="index.php?page=ban-user-admin" method="GET">
                <input type="hidden" name="page" value="ban-user-admin">
                <div class="search-input-group flex-grow-1">
                    <input type="text" class="form-control" placeholder="Rechercher par nom, prénom ou email..." name="search-user" value="<?= htmlspecialchars($_GET['search-user'] ?? '') ?>">
                </div>

                <select name="filter-role" class="search-select form-select w-auto">
                    <option value="">Tous les rôles</option>
                    <?php foreach ($listeRoles ?? [] as $role): ?>
                        <option value="<?= $role['role_id'] ?>" <?= (isset($_GET['filter-role']) && $_GET['filter-role'] == $role['role_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Rechercher</button>
                <?php if (!empty($_GET['search-user']) || !empty($_GET['filter-role'])): ?>
                    <a href="index.php?page=ban-user-admin" class="btn btn-secondary">Afficher tout</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tableau des utilisateurs -->
        <div class="card shadow-sm p-4">
            <div class="table-container">
                <table class="table-admin table align-middle">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Rôle</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listeUtilisateurs)): ?>
                            <?php foreach ($listeUtilisateurs as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user['nom']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= htmlspecialchars($user['role_nom']) ?></td>
        <td>
            <!-- On vérifie si l'utilisateur n'est PAS un administrateur (supposons role_id = 1 pour l'admin) -->
            <?php if ($user['role_id'] != 1): ?>
                <?php if ($user['est_actif'] == 1): ?>
                    <form action="index.php?page=ban-action-admin" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $user['utilisateur_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Désactiver</button>
                    </form>
                <?php else: ?>
                    <form action="index.php?page=unban-action-admin" method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $user['utilisateur_id'] ?>">
                        <button type="submit" class="btn btn-success btn-sm">Réactiver</button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <span class="badge bg-secondary">Protégé</span>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Aucun utilisateur trouvé.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>