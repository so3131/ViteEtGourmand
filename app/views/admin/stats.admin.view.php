
<?php
/** @var int $totalOrders */
/** @var float $caGlobal */
/** @var array $stats */
/** @var array $statsMenus */
/** @var float $chiffreAffaires */
/** @var array $filteredStats */
/** @var array $menus */
/** @var string $nomMenuSelectionne */
/** @var string|null $menuId */
/** @var string|null $dateDebut */
/** @var string|null $dateFin */
?>


<div class="container-fluid my-4">
    <h2 class="mb-4"><i class="fa-solid fa-chart-line me-2"></i> Tableau de bord & Statistiques</h2>
    
  
    <!-- Statistiques rapides -->
    <div class="row g-4 mb-4">
        
        <!-- Total Commandes -->
        <div class="col-md-3 col-sm-6">
            <div class="card p-4 shadow-sm border-0 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-0 text-muted">Total des commandes reçues</p><h3 class="text-primary mb-1">    <?= htmlspecialchars((string) ($totalOrders ?? 0), ENT_QUOTES, 'UTF-8') ?>
</h3>
                        
                    </div>
                    <div class="fs-2 text-primary opacity-50"><i class="fa-solid fa-utensils"></i></div>
                </div>
            </div>
        </div>

        <!-- CA Réel Global (Harmonisé) -->
        <div class="col-md-3 col-sm-6">
            <div class="card p-4 shadow-sm border-0 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-0 text-muted">CA Réel Global <br>( Hors caution)</p>
                        <h3 class="text-success mb-1"><?= number_format($caGlobal ?? 0, 2, ',', ' '); ?> €</h3>
                        
                    </div>
                    <div class="fs-2 text-success opacity-50"><i class="fa-solid fa-euro-sign"></i></div>
                </div>
            </div>
        </div>

        <!-- Total Menus -->
        <div class="col-md-3 col-sm-6">
            <div class="card p-4 shadow-sm border-0 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-0 text-muted">Menus proposés</p>
                        <h3 class="text-success mb-1"><?= htmlspecialchars($statsSql['total_menus'] ?? 0); ?></h3>
                        
                    </div>
                    <div class="fs-2 text-success opacity-50"><i class="fa-solid fa-book-open"></i></div>
                </div>
            </div>
        </div>

        <!-- Total Utilisateurs -->
        <div class="col-md-3 col-sm-6">
            <div class="card p-4 shadow-sm border-0 h-100">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1"><p class="mb-0 text-muted">Total des utilisateurs</p>
                        <h3 class="text-info mb-1"><?= htmlspecialchars($statsSql['total_users'] ?? 0); ?></h3>
                        
                    </div>
                    <div class="fs-2 text-info opacity-50"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- Statistiques secondaires (Thèmes & Régimes) -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted"><i class="fa-solid fa-tags me-2"></i> Total Thèmes</span>
                    <span class="badge bg-secondary fs-6"><?= htmlspecialchars($statsSql['total_themes'] ?? 0); ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm border-0">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted"><i class="fa-solid fa-seedling me-2"></i> Total Régimes</span>
                    <span class="badge bg-secondary fs-6"><?= htmlspecialchars($statsSql['total_regimes'] ?? 0); ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de filtre -->
<form method="GET" action="index.php" class="row g-3 align-items-end mb-4 p-3 bg-light rounded shadow-sm">
    <input type="hidden" name="page" value="stats-admin">
    
    <!-- Filtre par Menu -->
    <div class="col-md-3">
        <label for="menu_id" class="form-label">Menu :</label>
        <select name="menu_id" id="menu_id" class="form-select">
            <option value="">Tous les menus</option>
            <?php foreach ($menus as $menu): ?>
                <option value="<?= $menu['menu_id']; ?>" <?= (isset($_GET['menu_id']) && $_GET['menu_id'] == $menu['menu_id']) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($menu['titre']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Filtre par Période (Date début) -->
    <div class="col-md-3">
        <label for="date_debut" class="form-label">Du :</label>
        <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= htmlspecialchars($_GET['date_debut'] ?? ''); ?>">
    </div>

    <!-- Filtre par Période (Date fin) -->
    <div class="col-md-3">
        <label for="date_fin" class="form-label">Au :</label>
        <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= htmlspecialchars($_GET['date_fin'] ?? ''); ?>">
    </div>

    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
    </div>
</form>

<!-- Carte de détail dynamique si un menu spécifique est sélectionné -->
<?php if (!empty($menuId)): ?>
    <div class="card border-primary shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0 fs-5"><i class="fa-solid fa-circle-info me-2"></i> Détail du menu sélectionné</h4>
        </div>
        <div class="card-body">
            <div class="row text-center text-md-start">
                <div class="col-md-3 mb-2 mb-md-0">
                    <span class="text-muted d-block small">Menu ciblé</span>
                    <strong class="text-dark"><?= htmlspecialchars($nomMenuSelectionne ?? 'Inconnu'); ?></strong>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <span class="text-muted d-block small">Commandes filtrées</span>
                    <span class="badge bg-secondary fs-6"><?= $totalCommandesFiltrees ?? 0; ?></span>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <span class="text-muted d-block small">Chiffre d'affaires</span>
                    <span class="text-success fw-bold fs-5"><?= number_format($chiffreAffaires ?? 0, 2, ',', ' '); ?> €</span>
                </div>
                <div class="col-md-3">
                    <span class="text-muted d-block small">Période</span>
                    <span class="text-dark"><?= (!empty($dateDebut) || !empty($dateFin)) ? htmlspecialchars($dateDebut) . ' au ' . htmlspecialchars($dateFin) : 'Aucune restriction'; ?></span>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Alerte globale (ou filtrée par période) si aucun menu n'est sélectionné -->
<div class="alert alert-info shadow-sm mb-4">
    <h4 class="alert-heading mb-1"><i class="fa-solid fa-chart-line me-2"></i> 
        <?= (!empty($dateDebut) || !empty($dateFin)) ? "Vue d'ensemble sur la période" : "Vue d'ensemble globale"; ?>
    </h4>
    <p class="mb-0">
        Chiffre d'affaires <?= (!empty($dateDebut) || !empty($dateFin)) ? "sur la période" : "total"; ?> : 
        <strong><?= number_format($caGlobal ?? 0, 2, ',', ' '); ?> €</strong> 
        (Total commandes : <?= $totalOrders ?? 0; ?>)
    </p>
</div>
<?php endif; ?>

<!-- Zone Graphiques -->
<div class="card shadow-sm border-0 p-4 mb-4">
    <div class="row g-4">
        <!-- Graphique CA par Menu -->
        <div class="col-md-6">
            <div class="chart-wrapper">
                <div class="chart-header mb-3">
                    <h5 class="card-title"><i class="fa-solid fa-chart-pie me-2"></i> Chiffre d'affaires par Menu</h5>
                </div>
                <div class="chart-container" style="position: relative; height: 280px; width: 100%;">
                    <canvas id="caMenuChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Graphique Volume des commandes -->
        <div class="col-md-6">
            <div class="chart-wrapper">
                <div class="chart-header mb-3">
                    <h5 class="card-title"><i class="fa-solid fa-chart-bar me-2"></i> Volume des commandes</h5>
                    <p class="text-muted small">Nombre de ventes par menu</p>
                </div>
                <div class="chart-container" style="position: relative; height: 280px; width: 100%;">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const statsData = <?= json_encode(
        $statsMenus ?? [],
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) ?>;
</script>