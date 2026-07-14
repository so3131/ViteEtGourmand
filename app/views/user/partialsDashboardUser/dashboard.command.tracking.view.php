<?php
/** @var array $ordersData */
?>

<div class="tab-content">
    <?php foreach ($ordersData as $statut => $orders): ?>
        <div class="tab-pane fade <?= ($statut === 'en_attente') ? 'show active' : '' ?>" id="<?= $statut ?>">
            <?php if (empty($orders)): ?>
                <p class="text-muted">Aucune commande.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($orders as $order) renderOrderRow($order); ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
        