<?php
require_once dirname(__DIR__, 2) . '/Config/Constants.php';
?>

<main class="container-fluid py-4">

    <header class="mb-4 titre-search">
        <h1>Nos Menus</h1>
        <p class="text-muted">Découvrez nos créations et filtrez selon vos besoins.</p>
    </header>

    <div class="row">

        <aside class="col-md-3">
            <div class="card p-3 shadow-sm vg-filter-sticky">
                <h2 class="mb-3">Filtres</h2>
                <form id="filterForm">
                    <div class="filter-group border-bottom pb-3 mb-3">
                        <h3 class="h6 fw-bold">Budget</h3>
                        <label class="small text-muted">Fourchette de prix par personne</label>
                        <div id="price-slider" class="my-3"></div>

                        <input type="hidden" name="prix_min" id="input-slider-min" value="0">
                        <input type="hidden" name="prix_max_slider" id="input-slider-max" value="132">

                        <div class="d-flex justify-content-between mb-3">
                            <span id="slider-min" class="badge bg-light text-dark">0€</span>
                            <span id="slider-max" class="badge bg-light text-dark">132€</span>
                        </div>

                        <label for="price-max-select" class="small text-muted">Prix maximum</label>
                        <select class="form-select form-select-sm" name="prix_max" id="price-max-select">
                            <option value="">Tous les budgets</option>
                            <option value="30">Moins de 30€</option>
                            <option value="50">Moins de 50€</option>
                            <option value="100">Moins de 100€</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <h3 class="h6 fw-bold">Nombre de personnes</h3>
                        <label class="form-label">Choisissez un minimum d'invités</label>
                        <input type="number" class="form-control" name="nombre_personne_minimum" min="2" max="150" placeholder="Min 2 Personnes">
                    </div>

                    <div class="accordion accordion-flush" id="filterAccordion">
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#theme">Thèmes</button>
                            </h3>
                            <div id="theme" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                                <div class="accordion-body">
                                    <?php foreach ($themes ?? [] as $theme): ?>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="theme_id[]"
               value="<?= $theme['theme_id'] ?>" id="t<?= $theme['theme_id'] ?>">
        <label class="form-check-label" for="t<?= $theme['theme_id'] ?>">
            <?= htmlspecialchars($theme['libelle']) ?>
        </label>
    </div>
<?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#regime">Régimes</button>
                            </h3>
                            <div id="regime" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                                <div class="accordion-body">
                                    <?php foreach ($regimes ?? [] as $regime): ?>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="regime_id"
               value="<?= $regime['regime_id'] ?>" id="r<?= $regime['regime_id'] ?>">
        <label class="form-check-label" for="r<?= $regime['regime_id'] ?>">
            <?= htmlspecialchars($regime['libelle']) ?>
        </label>
    </div>
<?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-outline-primary w-100 mt-3">Appliquer les filtres</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100 mt-3" id="reset-filters">Réinitialiser les filtres</button>
                </form>
            </div>
        </aside>

        <section class="col-md-9">
            <div class="row row-cols-1 row-cols-lg-3 g-4" id="menu-container">

                <?php if (isset($menus) && !empty($menus)): ?>
                    <?php foreach ($menus as $menu): ?>
                        <div class="col">
                            <div class="card h-100 shadow-sm">
                                <?php
                                $plats = $menu['plats_structures'] ?? [];
                                $platPrincipal = $plats['Plat'] ?? $plats['Entrée'] ?? $plats['Dessert'] ?? null;
                                $photoUrl = !empty($platPrincipal['photo']) ? $platPrincipal['photo'] : 'assets/img/plats/default.webp';
                                ?>

                                <img src="<?= htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8') ?>"
                                    class="card-img-top"
                                    alt="Illustration <?= htmlspecialchars($menu['titre'], ENT_QUOTES, 'UTF-8') ?>"
                                    style="height: 300px; object-fit: cover;">

                                <div class="card-body">
                                    <h3 class="card-title h5"><?= htmlspecialchars($menu['titre']) ?></h3>
                                    <?php
                                    $theme = $menu['vg_theme']['libelle'] ?? 'Thème non défini';
                                    $regime = $menu['regime'] ?? 'Régime classique';
                                    ?>
                                    <h4 class="card-subtitle mb-2 text-muted h6">
                                        <?= htmlspecialchars($theme) ?> - <?= htmlspecialchars($regime) ?>
                                    </h4>
                                    <p class="card-text text-muted small"><?= htmlspecialchars($menu['description_menu']) ?></p>
                                    <ul class="list-unstyled small">
                                        <li><strong>Minimum :</strong> <?= htmlspecialchars($menu['nombre_personne_minimum']) ?> personnes</li>
                                        <li><strong>Prix :</strong> <?= number_format($menu['prix_par_personne'], 2) ?> € / pers.</li>
                                    </ul>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="index.php?page=details-menu&menu_id=<?= $menu['menu_id'] ?>" class="btn btn-primary w-100">
                                        Voir le détail
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 alert alert-info">Aucun menu disponible pour le moment.</div>
                <?php endif; ?>

            </div>
        </section>

    </div>
</main>