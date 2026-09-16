<!-- Step 4 : Paiement et finalisation. -->
<?php
/** @var int $menuID */
/** @var int $step */
?>
<!-- Step 2 : Options (matériel, etc.). -->
<div class="container mt-4">

    <div class="d-flex justify-content-between mb-4 bg-light p-3 rounded">
        <span class="badge <?= ($step == 0) ? 'bg-primary' : 'bg-secondary' ?>">0. Détails</span>
        <span class="badge <?= ($step == 1) ? 'bg-primary' : 'bg-secondary' ?>">1. Quantité</span>
        <span class="badge <?= ($step == 2) ? 'bg-primary' : 'bg-secondary' ?>">2. Options</span>
        <span class="badge <?= ($step == 3) ? 'bg-primary' : 'bg-secondary' ?>">3. Récap</span>
    </div>

    <div class="container mt-5">
        <div class="card p-4 shadow-sm text-center">
            <h3>Confirmation de paiement</h3>
            <p>Livraison prévue le : <?= htmlspecialchars($order['prestation']['date_prestation'] ?? '') ?></p>
            <p>Adresse : <?= htmlspecialchars($order['prestation']['adresse_precise'] ?? '') ?></p>
            <hr>
            <p class="lead">Montant total à régler : <strong><?= number_format($total_general ?? 0, 2) ?> €</strong></p>

            <form action="index.php?page=order-menu&step=5" method="POST">
                <input type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-credit-card"></i> Paiement sécurisé
                </button>
            </form>

            <div class="mt-3">
                <a href="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=3" class="text-muted">Annuler et retourner au récapitulatif</a>
            </div>
        </div>
    </div>