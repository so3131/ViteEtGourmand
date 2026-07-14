<?php

/**
 * @var array $menu
 * @var array $plats
 * @var array $allergenes
 * @var bool $canOrder
 */
?>





<div class="content-wrapper">

    <a href="index.php?page=search" class="btn btn-outline-secondary mb-4">&larr; Retour à la recherche</a>

    <div class="row">
        <div class="row">
            <div class="col-md-4">
                <div id="menuCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <?php foreach ($plats as $index => $plat): ?>
                            <button type="button" data-bs-target="#menuCarousel"
                                data-bs-slide-to="<?= $index ?>"
                                class="<?= $index === 0 ? 'active' : '' ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="carousel-inner">
                        <?php foreach ($plats as $index => $plat): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="render_image.php?id=<?php echo $plat['plat_id']; ?>"
                                    class="d-block w-100"
                                    alt="<?= htmlspecialchars($plat['titre_plat'] ?? 'Plat sans nom') ?>">
                                <div class="carousel-caption">
                                    <h5><?= htmlspecialchars($plat['titre_plat']) ?></h5>
                                    <p><?= htmlspecialchars($plat['description_plat']) ?></p>
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
            </div>

            <div class="col-md-4">
                <h1 class="fw-bold"><?= htmlspecialchars($menu['titre']) ?></h1>
                <span class="badge bg-primary"><?= htmlspecialchars($menu['theme_libelle']) ?></span>
                <span class="badge bg-info text-dark"><?= htmlspecialchars($menu['regime_libelle']) ?></span>

                <p class="mt-4 lead"><?= htmlspecialchars($menu['description_menu']) ?></p>

                <h4 class="mt-4">Composition du menu</h4>
                <ul class="list-group list-group-flush mb-4">
                    <?php foreach ($plats as $plat): ?>
                        <li class="list-group-item"><?= htmlspecialchars($plat['titre_plat']) ?> - <?= htmlspecialchars($plat['description_plat']) ?></li>
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
                </ul>
            </div>
            <div class="col-md-4">
                <div class="card p-4 shadow-sm">
                    <h5 class="text-muted">À partir de</h5>
                    <h2 class="display-5 fw-bold"><?= number_format($menu['prix_par_personne'], 2) ?>€</h2>
                    <hr>
                    <p>Minimum : <strong><?= $menu['nombre_personne_minimum'] ?> pers.</strong></p>

                    <p>Quantité restante : <strong><?= $menu['quantite_restante'] ?></strong></p>
                    <?php

                    if (!empty($menu['delai_commande']) && $menu['delai_commande'] > 0):
                    ?>
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
                   <?php if ($canOrder): ?>
    <a href="index.php?page=order-menu&menu_id=<?= $menu['menu_id'] ?>&step=0" 
       class="btn btn-primary btn-lg w-100">
       Commander ce menu
    </a>
<?php else: ?>
    <a href="index.php?page=login&redirect=<?= urlencode('index.php?page=order-menu&menu_id=' . $menu['menu_id'] . '&step=0') ?>" 
       class="btn btn-primary btn-lg w-100">
       Nous rejoindre pour commander
    </a>
<?php endif; ?>
                    
                </div>


            </div>

        </div>
    </div>