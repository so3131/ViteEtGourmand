<?php

/** @var array $commande */
?>
<div class="card shadow-sm p-4">
    <h4 class="mb-3">Laisser un avis sur une commande</h4>

    <form action="index.php?page=dashboard-user" method="POST">
        <input type="hidden" name="action" value="store_review">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="mb-3">
            <label class="form-label">Sélectionner une commande terminée</label>
            <select name="commande_id" class="form-select" required>
                <option value="">-- Choisissez une commande --</option>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php if ($order['statut'] === 'terminee'): ?>
                            <option value="<?= $order['commande_id'] ?>">
                                Commande n°<?= htmlspecialchars($commande['numero_commande']) ?> du <?= date('d/m/Y', strtotime($order['date_commande'])) ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Note (de 1 à 5)</label>
            <select name="rating" class="form-select" required style="width: 150px;">
                <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                <option value="4">⭐⭐⭐⭐ (4/5)</option>
                <option value="3">⭐⭐⭐ (3/5)</option>
                <option value="2">⭐⭐ (2/5)</option>
                <option value="1">⭐ (1/5)</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Votre commentaire</label>
            <textarea name="comment" class="form-control" rows="3" required placeholder="Qu'avez-vous pensé de votre prestation ?"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Publier l'avis</button>
    </form>
</div>