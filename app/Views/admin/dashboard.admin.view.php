<?php

/**
 * Variables passées depuis le contrôleur DashboardAdminController
 * @var int $pendingOrders
 * @var int $finishedOrders
 * @var int $totalOrders
 * @var int $pendingReviews
 * @var float $todaySales
 * @var int $todayOrders
 * @var array $horairesList
 * @var int $ruptureCount
 * @var int $pendingReturnOrders
 */
?>

<div class="container py-4">
    <h1 class="mb-4">Tableau de bord Admin</h1>

    <!-- SECTION DES COMPTEURS CLIQUABLES -->
    <div class="row mb-5">

        <!-- 1. Commandes en attente -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=order-management&client_nom=&status=en_attente" class="text-decoration-none">
                <div class="card bg-warning text-dark shadow-sm h-100 card-hover p-2">
                    <div class="card-body py-2 px-3">
                        <h2 class="card-title text-uppercase fw-bold fs-7 mb-1">Commandes en attente</h2>
                        <p class="fs-2 fw-bold mb-1"><?= $pendingOrders ?></p>
                        <small class="text-dark fw-semibold">Voir la liste →</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Commandes terminées -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=order-management&client_nom=&status=terminee" class="text-decoration-none">
                <div class="card bg-success text-white shadow-sm h-100 card-hover p-2">
                    <div class="card-body py-2 px-3">
                        <h2 class="card-title text-uppercase fw-bold fs-7 mb-1">Commandes terminées</h2>
                        <p class="fs-2 fw-bold mb-1"><?= $finishedOrders ?></p>
                        <small class="text-black fw-semibold">Voir la liste →</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Chiffre d'affaires du jour -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=stats-admin" class="text-decoration-none">
                <div class="card bg-warning text-dark shadow-sm h-100 card-hover p-2">
                    <div class="card-body py-2 px-3">
                        <h2 class="card-title text-uppercase fw-bold fs-7 mb-1">Chiffre d'affaires du jour</h2>
                        <p class="fs-2 fw-bold mb-1"><?= number_format($todaySales, 2, ',', ' ') ?> €</p>
                        <small class="text-dark fw-semibold">Voir les détails →</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Avis à valider -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=review-management&status=pending" class="text-decoration-none">
                <div class="card bg-info text-dark shadow-sm h-100 card-hover p-2">
                    <div class="card-body py-2 px-3">
                        <h2 class="card-title text-uppercase fw-bold fs-7 mb-1">Avis à valider</h2>
                        <p class="fs-2 fw-bold mb-1"><?= $pendingReviews ?></p>
                        <small class="text-dark fw-semibold">Modérer →</small>
                    </div>
                </div>
            </a>
        </div>
        <!-- 5. Rupture de stock -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=menu-management" class="text-decoration-none">
                <div class="card bg-dark text-white shadow-sm h-100 card-hover p-2">
                    <div class="card-body py-2 px-3">
                        <h2 class="card-title text-uppercase fw-bold fs-7 mb-1">Rupture de stock</h2>
                        <p class="fs-2 fw-bold mb-1"><?= $ruptureCount ?></p>
                        <small class="text-warning fw-semibold">Voir les menus →</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Carte Total des commandes en attente de retour materiel -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=order-management&client_nom=&status=en_attente_retour_materiel" class="text-decoration-none">
                <div class="card bg-primary text-white shadow-sm h-100 card-hover">
                    <div class="card-body py-2 px-3">
                        <h2 class="card-title text-uppercase fw-bold fs-7 mb-1">En attente de retour materiel</h2>
                        <p class="display-5 fw-bold mb-0"><?= $pendingReturnOrders ?></p>
                        <small class="text-white-50">Voir tout →</small>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<!-- Zone de Travail Principale -->
<div class="card shadow-sm p-4">
    <!-- Section Horaires -->
    <div class="row">
        <!-- Colonne de gauche : Le tableau récapitulatif -->
        <div class="col-md-8">
            <div class="card shadow-sm p-4 mb-4">
                <h3 class="mb-3">Horaires actuels de la semaine</h3>
                <div>
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Jour</th>
                                <th>Ouverture</th>
                                <th>Fermeture</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($horairesList)): ?>
                                <?php foreach ($horairesList as $h): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($h['jour']) ?></strong></td>
                                        <td><?= $h['heure_ouverture'] ? htmlspecialchars($h['heure_ouverture']) : '<span class="text-muted">Fermé</span>' ?></td>
                                        <td><?= $h['heure_fermeture'] ? htmlspecialchars($h['heure_fermeture']) : '<span class="text-muted">Fermé</span>' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucun horaire enregistré.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Colonne de droite : Le formulaire de modification -->
        <div class="col-md-4">
            <div class="card shadow-sm p-4 mb-4">
                <h3 class="mb-3">Modifier un jour</h3>
                <form action="?page=dashboard-admin" method="POST"> <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="mb-3">
                        <label for="jour" class="form-label">Jour</label>
                        <select name="jour" id="jour" class="form-select" required>
                            <option value="Lundi">Lundi</option>
                            <option value="Mardi">Mardi</option>
                            <option value="Mercredi">Mercredi</option>
                            <option value="Jeudi">Jeudi</option>
                            <option value="Vendredi">Vendredi</option>
                            <option value="Samedi">Samedi</option>
                            <option value="Dimanche">Dimanche</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="est_ferme" id="est_ferme" value="1">
                        <label class="form-check-label text-danger fw-bold" for="est_ferme">
                            Fermé ce jour
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="heure_ouverture" class="form-label">Heure d'ouverture</label>
                        <input type="time" name="heure_ouverture" id="heure_ouverture" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="heure_fermeture" class="form-label">Heure de fermeture</label>
                        <input type="time" name="heure_fermeture" id="heure_fermeture" class="form-control">
                    </div>
                    <button type="submit" name="update_horaire" class="btn btn-primary w-100">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

</div>