<!-- Step 1 : Récupération de la quantité et calcul du prix total du menu. -->
<?php
/** @var int $menuID */
/** @var \App\Models\Menu $menu */
/** @var int $step */
/** @var array $menuInfo */
?>

<main class="container mt-4">

    <?php include ROOT_PATH . '/app/Views/user/order/order.steps.view.php'; ?>

    <div id="step-1" class="card p-4 shadow-sm">
        <form action="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=1" method="POST">
            <input type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

            <div class="mb-3">
                <h1 class=" h4 mb-4">Choisir le nombre de <?= htmlspecialchars($menuInfo['titre'] ?? 'Menu inconnu') ?></h1>
                <p class="form-label">
                    Nous pouvons actuellement vous préparer <?= htmlspecialchars($menuInfo['quantite_restante'] ?? 0) ?> <?= htmlspecialchars($menuInfo['titre'] ?? 'Menu inconnu') ?>.
                </p>
                <br>

                <input type="number" name="nombre_personne" id="nombre_personne" class="form-control"
                    min="<?= $menu ? $menu->getMinimumRequis() : 1 ?>"
                    max="<?= $menuInfo['quantite_restante'] ?? 0 ?>" required
                    value="<?= htmlspecialchars($_SESSION['current_order']['menu']['quantite'] ?? ($menu ? $menu->getMinimumRequis() : 1)) ?>">
                <label for="nombre_personne" class="form-label">
                    Nombre de personnes (Minimum requis :
                    <?= htmlspecialchars($menu->getMinimumRequis() ?? 0) ?>
                    personnes) :
                </label>
            </div>

            <div class="d-flex justify-content-between">
                <a href="index.php?page=search" class="btn btn-secondary me-2">Annuler la commande</a>
                <a href="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=0" class="btn btn-outline-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Passer à l'étape suivante</button>
            </div>

        </form>
    </div>
</main>