<!-- Step 1 : Récupération de la quantité et calcul du prix total du menu. -->
 <?php
/** @var int $menuID */
/** @var \App\Models\Menu $menu */
/** @var int $step */
/** @var array $menuInfo */
?>

<div class="container mt-4">
<h1 class="mb-4">Choisir le nombre de <?= htmlspecialchars($menuInfo['titre'] ?? 'Menu inconnu') ?></h1>

    <?php $step = $step ?? 1; ?>
    <div class="d-flex justify-content-between mb-4 bg-light p-3 rounded">
        <span class="badge <?= ($step == 0) ? 'bg-primary' : 'bg-secondary' ?>">0. Détails</span>
        <span class="badge <?= ($step == 1) ? 'bg-primary' : 'bg-secondary' ?>">1. Quantité</span>
        <span class="badge <?= ($step == 2) ? 'bg-primary' : 'bg-secondary' ?>">2. Options</span>
        <span class="badge <?= ($step == 3) ? 'bg-primary' : 'bg-secondary' ?>">3. Récap</span>
    </div>

    <div id="step-1" class="card p-4 shadow-sm">
        <form action="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=1" method="POST">
           

            <div class="mb-3">
                <label for="quantite_restante" class="form-label">
                   Nous pouvons actuellement vous preparer <?= htmlspecialchars($menuInfo['quantite_restante'] ?? 0) ?> <?= htmlspecialchars($menuInfo['titre'] ?? 'Menu inconnu') ?>
                </label>
                <br>
                <label for="nombre_personne" class="form-label">
                   Nombre de personnes (Minimum requis : 
    <?= htmlspecialchars($menu->getMinimumRequis() ?? 0) ?> 
    personnes) :
                </label>
                <input type="number" name="nombre_personne" id="nombre_personne" class="form-control"
                    min="<?= $menu ? $menu->getMinimumRequis() : 1 ?>"
                    max="<?= $menuInfo['quantite_restante'] ?? 0 ?>" required
                    value="<?= htmlspecialchars($_SESSION['current_order']['menu']['quantite'] ?? ($menu ? $menu->getMinimumRequis() : 1)) ?>">
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php?page=search" class="btn btn-secondary me-2">Annuler la commande</a>
                <a href="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=0" class="btn btn-outline-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Passer à l'étape suivante</button>
            </div>

        </form>
    </div>
</div>