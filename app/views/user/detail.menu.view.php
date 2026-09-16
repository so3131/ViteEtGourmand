<?php

/**
 * @var array $menu
 * @var array $plats
 * @var array $allergenes
 * @var bool $canOrder
 */
?>

<div class="content-wrapper container py-4">
    <div class="row">
        <!-- Colonne 1 : Carrousel des plats et bouton retour -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div id="menuCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php foreach ($plats as $index => $plat): ?>
                        <button type="button" data-bs-target="#menuCarousel"
                            data-bs-slide-to="<?= $index ?>"
                            class="<?= $index === 0 ? 'active' : '' ?>">
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-inner rounded shadow-sm">
                    <?php foreach ($plats as $index => $plat): ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= !empty($plat['photo']) ? htmlspecialchars($plat['photo']) : 'assets/img/plats/default.webp' ?>"
                                class="d-block w-100 rounded"
                                style="height: 300px; object-fit: cover;"
                                alt="<?= htmlspecialchars($plat['titre_plat'] ?? 'Plat sans nom') ?>">
                            
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                                <h5><?= htmlspecialchars($plat['titre_plat']) ?></h5>
                                <p class="m-0"><?= htmlspecialchars($plat['description_plat']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#menuCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#menuCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>

            <!-- Bouton retour repositionné proprement hors du carrousel avec une marge au-dessus -->
            <div class="mt-4">
                <a href="index.php?page=search" class="btn btn-outline-secondary">&larr; Retour à la recherche</a>
            </div>
        </div>

        <!-- Colonne 2 : Informations et composition du menu -->
        <div class="col-lg-4 col-md-6 mb-4">
            <h1 class="fw-bold"><?= htmlspecialchars($menu['titre']) ?></h1>
            <span class="badge bg-primary"><?= htmlspecialchars($menu['theme_libelle'] ?? '') ?></span>
            <span class="badge bg-info text-dark"><?= htmlspecialchars($menu['regime_libelle'] ?? '') ?></span>

            <p class="mt-4 lead"><?= htmlspecialchars($menu['description_menu']) ?></p>

            <h4 class="mt-4">Composition du menu</h4>
            <ul class="list-group list-group-flush mb-4">
                <?php foreach ($plats as $plat): ?>
                    <li class="list-group-item bg-transparent"><?= htmlspecialchars($plat['titre_plat']) ?> - <?= htmlspecialchars($plat['description_plat']) ?></li>
                <?php endforeach; ?>
            </ul>

            <h4 class="mt-4">Allergènes</h4>
            <p>
                <?php if (!empty($allergenes)): ?>
                    <?php foreach ($allergenes as $a): ?>
                        <span class="badge bg-warning text-dark me-1"><?= htmlspecialchars($a['libelle']) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    Aucun allergène particulier.
                <?php endif; ?>
            </p>
        </div>

        <!-- Colonne 3 : Carte de commande et tarifs -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card p-4 shadow-sm">
                <h5 class="text-muted">À partir de</h5>
                <h2 class="display-5 fw-bold"><?= number_format($menu['prix_par_personne'], 2) ?>€</h2>
                <hr>
                <p>Minimum : <strong><?= $menu['nombre_personne_minimum'] ?> pers.</strong></p>
                <p>Quantité restante : <strong><?= $menu['quantite_restante'] ?></strong></p>
                
                <?php if (!empty($menu['delai_commande']) && $menu['delai_commande'] > 0): ?>
                    <p>Délais de commande : <strong><?= htmlspecialchars($menu['delai_commande']) ?> jours</strong></p>
                <?php else: ?>
                    <p>Délais de commande : <strong>24h</strong></p>
                <?php endif; ?>

                <p>
                    Précautions de stockage :
                    <strong>
                        <?= !empty($menu['conditions_stockage']) ? htmlspecialchars($menu['conditions_stockage']) : 'Aucune précaution particulière' ?>
                    </strong>
                </p>

                <?php if ((int)($menu['quantite_restante'] ?? 0) <= 0): ?>
                    <!-- Menu en rupture de stock -->
                    <button class="btn btn-secondary btn-lg w-100" disabled>
                        <i class="fa-solid fa-triangle-exclamation"></i> Rupture de stock
                    </button>
                <?php elseif ($canOrder): ?>
                    <!-- Utilisateur connecté et peut commander -->
                    <a href="index.php?page=order-menu&menu_id=<?= $menu['menu_id'] ?>&step=0" 
                        class="btn btn-primary btn-lg w-100">
                        Commander ce menu
                    </a>
                <?php else: ?>
                    <!-- Utilisateur non connecté -->
                    <a href="index.php?page=login&redirect=<?= urlencode('index.php?page=order-menu&menu_id=' . $menu['menu_id'] . '&step=0') ?>" 
                        class="btn btn-primary btn-lg w-100">
                        Nous rejoindre pour commander
                    </a>
                <?php endif; ?>

                <!-- Message staff -->
                <?php if (isset($_SESSION['role_id']) && in_array((int)$_SESSION['role_id'], [ROLE_ADMIN, ROLE_EMPLOYE])): ?>
                    <div class="alert alert-warning mt-3 mb-0">
                        <strong>Note :</strong> En tant que membre du staff, vous ne pouvez pas passer de commandes. Merci de vous connecter en tant qu'utilisateur pour commander.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>