<!-- Step 4 : Paiement et finalisation. -->
<?php
/** @var int $menuID */
/** @var int $step */
?>

<main class="container mt-4">

    <?php include ROOT_PATH . '/app/Views/user/order/order.steps.view.php'; ?>

    <div class="container mt-5">
        <div class="card p-4 shadow-sm text-center">
            <h1 class="h3">Paiement</h1>
            <p>Livraison prévue le : <?= htmlspecialchars($order['prestation']['date_prestation'] ?? '') ?></p>
            <p>Adresse : <?= htmlspecialchars($_SESSION['current_order']['prestation']['adresse_livraison'] ?? '') ?></p>
            <hr>
            <p class="lead">Montant total à régler : <strong><?= number_format($total_general ?? 0, 2) ?> €</strong></p>

            <form action="index.php?page=order-menu&step=5" method="POST">
                <input type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-credit-card"></i> Paiement sécurisé<br> (Rappel : Ce site est un projet de démonstration (ECF))
                </button>
            </form>

            <div class="mt-3">
                <a href="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=3" class="text-muted">Annuler et retourner au récapitulatif</a>
            </div>
        </div>
    </div>
</main>