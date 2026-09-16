<div class="container-fluid px-4 py-3">

    <!-- ========================================== -->
    <!-- SECTION 1 : GESTION DES EMPLOYÉS (RH)     -->
    <!-- ========================================== -->
    <div class="admin-section mb-5">
        <div class="section-header mb-3">
            <h3 class="mb-1"><i class="fa-solid fa-user-shield"></i> Gestion de l'équipe</h3>
            <p class="text-muted small mb-0">Ajoutez de nouveaux employés et gérez les accès de votre équipe actuelle.</p>
        </div>

        <!-- Bloc Formulaire d'ajout-->
        <div class="card shadow-sm p-3 mb-4">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-user-plus"></i> Ajouter un employé</h6>
            <form action="index.php?page=rh-admin-create" method="POST" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <?php render_form_input('email', 'Email (Username)', 'email', 'Entrez l\'email...', ''); ?>
                    </div>
                    <div class="col-md-5">
                        <?php render_form_input('password', 'Mot de passe temporaire', 'password', 'Mot de passe...', '','new-password'); ?>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100 py-2">Créer</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bloc Liste de l'équipe actuelle -->
        <div class="card shadow-sm p-3">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-users"></i> Équipe actuelle</h6>
            <div class="table-responsive">
                <table class="table table-sm table-admin align-middle mb-0" id="employesTable">
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Email</th>
                            <th>Arrivée</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
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
                                            <div class="user-avatar-sm"><?= $initiales ?></div>
                                            <strong class="small"><?= htmlspecialchars($emailParts[0]) ?></strong>
                                        </div>
                                    </td>
                                    <td class="small"><?= htmlspecialchars($emp['email']) ?></td>
                                    <td class="small"><?= isset($emp['created_at']) ? date('d/m/Y', strtotime($emp['created_at'])) : 'N/C' ?></td>
                                    <td>
                                        <?php if (isset($emp['est_actif']) && $emp['est_actif'] == 1): ?>
                                            <span class="badge bg-success font-xs">Actif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger font-xs">Inactif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-xs btn-outline-secondary" title="Réinitialiser MDP"><i class="fa-solid fa-key"></i></button>
                                            <?php if (isset($emp['est_actif']) && $emp['est_actif'] == 1): ?>
                                                <form action="index.php?page=rh-admin-toggle" method="POST" class="d-inline" onsubmit="return confirm('Désactiver cet employé ?');">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                    <input type="hidden" name="id" value="<?= $emp['utilisateur_id'] ?>">
                                                    <button type="submit" class="btn btn-xs btn-outline-danger" title="Désactiver"><i class="fa-solid fa-user-lock"></i></button>
                                                </form>

                                            <?php else: ?>
                                                <form action="index.php?page=rh-admin-toggle" method="POST" class="d-inline" onsubmit="return confirm('Réactiver cet employé ?');">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                    <input type="hidden" name="id" value="<?= $emp['utilisateur_id'] ?>">
                                                    <button type="submit" class="btn btn-xs btn-outline-success" title="Réactiver"><i class="fa-solid fa-user-check"></i></button>
                                                </form>

                                            <?php endif; ?>
                                            <form action="index.php?page=rh-admin-delete" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet employé ?');">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                <input type="hidden" name="id" value="<?= $emp['utilisateur_id'] ?>">
                                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Supprimer"><i class="fa-solid fa-user-slash"></i></button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted small py-3">Aucun employé enregistré.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <!-- ========================================== -->
    <!-- SECTION 2 : MODÉRATION GLOBALE DES USERS  -->
    <!-- ========================================== -->
    <div class="admin-section">
        <div class="section-header mb-3">
            <h3 class="mb-1"><i class="fas fa-users-gear"></i> Modération des utilisateurs</h3>
            <p class="text-muted small mb-0">Recherchez un utilisateur pour gérer ses accès et son statut sur le site.</p>
        </div>

        <!-- Barre de recherche et filtres compacte -->
        <div class="search-bar-container card shadow-sm p-3 mb-3">
            <form class="search-ban-form d-flex gap-2 align-items-center flex-wrap" action="index.php?page=rh-admin#search-section" method="GET">
                <input type="hidden" name="page" value="rh-admin">
                <div class="search-input-group flex-grow-1">
                    <input type="text" class="form-control form-control-sm" placeholder="Rechercher par nom ou email..." name="search-user" value="<?= htmlspecialchars($_GET['search-user'] ?? '') ?>">
                </div>

                <select name="filter-role" class="search-select form-select form-select-sm w-auto">
                    <option value="">Tous les rôles</option>
                    <?php foreach ($listeRoles ?? [] as $role): ?>
                        <option value="<?= $role['role_id'] ?>" <?= (isset($_GET['filter-role']) && $_GET['filter-role'] == $role['role_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Rechercher</button>
                <?php if (!empty($_GET['search-user']) || !empty($_GET['filter-role'])): ?>
                    <a href="index.php?page=rh-admin#search-section" class="btn btn-outline-secondary btn-sm">Effacer</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tableau des utilisateurs condensé -->
        <div class="card shadow-sm p-3"id="search-section">
            <div class="table-responsive">
                <table class="table table-sm table-admin align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($listeUtilisateurs)): ?>
                            <?php foreach ($listeUtilisateurs as $user): ?>
                                <tr>
                                    <td class="small fw-semibold"><?= htmlspecialchars($user['nom']) ?></td>
                                    <td class="small"><?= htmlspecialchars($user['email']) ?></td>
                                    <td><span class="badge bg-secondary font-xs"><?= htmlspecialchars($user['role_nom']) ?></span></td>
                                    <td class="text-end">
                                        <?php if ($user['role_id'] != 1): ?>
                                            <?php if ($user['est_actif'] == 1): ?>
                                                <form action="index.php?page=ban-user#search-section" method="POST" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                    <input type="hidden" name="id" value="<?= $user['utilisateur_id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-xs">Désactiver</button>
                                                </form>
                                            <?php else: ?>
                                                <form action="index.php?page=unban-user#search-section" method="POST" class="d-inline"> <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                                    <input type="hidden" name="id" value="<?= $user['utilisateur_id'] ?>">
                                                    <button type="submit" class="btn btn-success btn-xs">Réactiver</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark border">Protégé</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted small py-3">Aucun utilisateur trouvé.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>