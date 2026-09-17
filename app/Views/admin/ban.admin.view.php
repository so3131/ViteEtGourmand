<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success_message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif;

/** @var array $listeRoles */
/** @var array $listeUtilisateurs */
?>
<div class="admin-section">
    <div class="section-header">
        <h2><i class="fas fa-user-shield"></i> Modération des comptes</h2>
        <p>Recherchez un client, un employé ou un administrateur pour gérer ses accès.</p>
    </div>

    <div class="search-bar-container">
        <form class="search-ban-form" action="index.php?page=ban-user-admin" method="GET">
            <input type="hidden" name="page" value="ban-user-admin">
            <div class="search-input-group">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Rechercher par nom, prénom ou email..." name="search-user" value="<?= htmlspecialchars($_GET['search-user'] ?? '') ?>">
            </div>

            <select name="filter-role" class="search-select">
                <option value="">Tous les rôles</option>
                <?php foreach ($listeRoles as $role): ?>
                    <option value="<?= $role['role_id'] ?>" <?= (isset($_GET['filter-role']) && $_GET['filter-role'] == $role['role_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($role['libelle']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-moderation btn-valider">Rechercher</button>
            <?php if (!empty($_GET['search-user']) || !empty($_GET['filter-role'])): ?>
                <a href="index.php?page=ban-user-admin" class="btn btn-secondary ms-2">
                    Afficher tout
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="table-container mt-4">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Rôle</th>
                    <th>Email</th>
                    <th>Signalements</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($listeUtilisateurs)): ?>
                    <?php foreach ($listeUtilisateurs as $user): ?>
                        <tr class="<?= isset($user['est_actif']) && $user['est_actif'] == 0 ? 'account-disabled' : '' ?>">
                            <td><strong><?= (!empty($user['prenom']) && !empty($user['nom']))
                                            ? htmlspecialchars($user['prenom'] . ' ' . $user['nom'])
                                            : (!empty($user['pseudo']) ? htmlspecialchars($user['pseudo']) : 'Utilisateur') ?>
                                </strong></td>
                            <td><span class="role-badge <?= strtolower($user['role_nom'] ?? '') ?>"><?= htmlspecialchars($user['role_nom'] ?? 'Inconnu') ?></span></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><span class="warning-count"><?= $user['signalements_count'] ?? 0 ?></span></td>
                            <td>
                                <!-- Si l'utilisateur est actif, on affiche le bouton de bannissement, sinon le bouton de réactivation -->
                                <?php if (!isset($user['est_actif']) || (int)$user['est_actif'] === 1): ?>
                                    <form action="index.php?page=ban-user" method="POST"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir désactiver cet utilisateur ?');"
                                        style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                        <input type="hidden" name="id" value="<?= $user['utilisateur_id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Désactiver</button>
                                    </form>
                                <?php else: ?>
                                    <form action="index.php?page=unban-user" method="POST"
                                        onsubmit="return confirm('Êtes-vous sûr de vouloir réactiver cet utilisateur ?');"
                                        style="display:inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                                        <input type="hidden" name="id" value="<?= $user['utilisateur_id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">Réactiver</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">Aucun utilisateur trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>