<?php
/**
 * Variables passées depuis le contrôleur DashboardAdminController
 * @var int $pendingOrders
 * @var int $finishedOrders
 * @var int $totalOrders
 * @var int $pendingReviews
 */
?>
<div class="container py-4">
    <h1 class="mb-4">Tableau de bord Employé</h1>

    <!-- SECTION DES COMPTEURS CLIQUABLES -->
    <div class="row mb-5">
        
        <!-- Carte Commandes en attente -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=order-management&client_nom=&status=en_attente" class="text-decoration-none">
                <div class="card bg-warning text-dark shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase fw-bold">Commandes en attente</h6>
                        <p class="display-5 fw-bold mb-0"><?= $pendingOrders ?></p>
                        <small class="text-dark fw-semibold">Voir la liste →</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Carte Commandes terminées -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=order-management&client_nom=&status=terminee" class="text-decoration-none">
                <div class="card bg-success text-white shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase fw-bold">Commandes terminées</h6>
                        <p class="display-5 fw-bold mb-0"><?= $finishedOrders ?></p>
                        <small class="text-white-50">Voir la liste →</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Carte Total des commandes -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=order-management&client_nom=&status=" class="text-decoration-none">
                <div class="card bg-primary text-white shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase fw-bold">Total Commandes</h6>
                        <p class="display-5 fw-bold mb-0"><?= $totalOrders ?></p>
                        <small class="text-white-50">Voir tout →</small>
                    </div>
                </div>
            </a>
        </div>

        <!-- Carte Avis à valider -->
        <div class="col-md-3 mb-3">
            <a href="index.php?page=review-management&status=pending" class="text-decoration-none">
                <div class="card bg-info text-dark shadow-sm h-100 card-hover">
                    <div class="card-body">
                        <h6 class="card-title text-uppercase fw-bold">Avis à valider</h6>
                        <p class="display-5 fw-bold mb-0"><?= $pendingReviews ?></p>
                        <small class="text-dark fw-semibold">Modérer →</small>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>