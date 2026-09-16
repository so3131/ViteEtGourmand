<?php

/** @var int $menuID */
/** @var \App\Models\Menu $menu */
/** @var int $step */
/** @var array $orderData */
?>

<div class="container py-5">
    <!-- Barre d'étape (Steppers) -->
    <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between position-relative bg-light p-3 rounded shadow-sm">
                <span class="badge <?= ($step == 0) ? 'bg-primary' : 'bg-secondary' ?> px-3 py-2">0. Détails</span>
                <span class="badge <?= ($step == 1) ? 'bg-primary' : 'bg-secondary' ?> px-3 py-2">1. Quantité</span>
                <span class="badge <?= ($step == 2) ? 'bg-primary' : 'bg-secondary' ?> px-3 py-2">2. Options</span>
                <span class="badge <?= ($step == 3) ? 'bg-primary' : 'bg-secondary' ?> px-3 py-2">3. Récap</span>
            </div>
        </div>
    </div>

    <!-- Carte principale du récapitulatif -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <h2 class="h3 fw-bold text-dark mb-4 text-center">Récapitulatif de votre commande</h2>

                    <!-- Section Informations de prestation -->
                    <div class="mb-4">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-geo-alt-fill me-2"></i>Informations de prestation
                        </h5>
                        <div class="row g-2 text-muted">
                            <div class="col-sm-6">
                                <strong>Date :</strong> <?= htmlspecialchars($orderData['prestation']['date_prestation'] ?? 'Non défini') ?>
                            </div>
                            <div class="col-sm-6">
                                <strong>Heure :</strong> <?= htmlspecialchars($orderData['prestation']['heure_livraison'] ?? 'Non défini') ?>
                            </div>
                            <div class="col-12">
                                <strong>Lieu :</strong> <?= htmlspecialchars($orderData['prestation']['ville'] ?? 'Non défini') ?>
                            </div>
                        </div>
                    </div>

                    <!-- Section Détails du menu -->
                    <div class="mb-4">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-basket-fill me-2"></i>Détails du menu
                        </h5>
                        <div class="row g-2 text-muted">
                            <div class="col-12">
                                <strong>Menu :</strong> <?= htmlspecialchars($orderData['prestation']['nom_menu'] ?? 'Non défini') ?>
                            </div>
                            <div class="col-sm-6">
                                <strong>Nombre de personnes :</strong> <span class="badge bg-info text-dark"><?= htmlspecialchars($orderData['menu']['quantite'] ?? 0) ?></span>
                            </div>
                            <div class="col-sm-6">
                                <strong>Location de matériel :</strong>
                                <span class="badge <?= (isset($orderData['options']['location_materiel']) && $orderData['options']['location_materiel']) ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= (isset($orderData['options']['location_materiel']) && $orderData['options']['location_materiel']) ? 'Oui' : 'Non' ?>
                                </span>
                            </div>
                            <div class="col-12">
                                <strong>Prix unitaire :</strong> <?= number_format($orderData['menu']['prix_unitaire'] ?? 0, 2) ?> €
                            </div>
                        </div>
                    </div>

                    <!-- Section Votre commande / Tarifs -->
                    <div class="mb-4">
                        <h5 class="text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-receipt me-2"></i>Facturation
                        </h5>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Prix du menu
                                <span><?= number_format($orderData['menu']['prix_menu_total'] ?? 0, 2) ?> €</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Frais de livraison
                                <span><?= number_format($orderData['prestation']['frais_livraison'] ?? 0, 2) ?> €</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Dépôt de garantie
                                <span><?= number_format($orderData['prestation']['depot_garantie'] ?? 0, 2) ?> €</span>
                            </li>
                        </ul>

                        <?php if (!empty($Discount)): ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="bi bi-check-circle-fill flex-shrink-0 me-2"></i>
                                <div>
                                    <strong>Bonne nouvelle !</strong> Une réduction de 10% a été appliquée car vous avez commandé pour au moins 5 personnes de plus que le minimum requis.
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Total général -->
                        <div class="card bg-light border-0 p-3 mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 mb-0 fw-bold text-dark">Total à payer :</span>
                                <span class="h4 mb-0 fw-bold text-primary"><?= number_format($total_general ?? 0, 2) ?> €</span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire / Boutons d'action -->
                    <form action="index.php?page=order-menu&step=4" method="POST" class="d-flex justify-content-between align-items-center mt-4">
                        <input type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <a href="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=2" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-arrow-left me-2"></i>Retour
                        </a>
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow-sm">
                            Confirmer et payer <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>