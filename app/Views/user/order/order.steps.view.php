<?php
$step = $step ?? 0;
$labels = ['Détails', 'Quantité', 'Options', 'Récap', 'Paiement'];
$pourcentage = min(100, (($step + 1) / count($labels)) * 100);
?>
<nav aria-label="Progression de la commande" class="mb-4">
    <div class="progress" style="height: 6px;" aria-hidden="true">
        <div class="progress-bar" style="width: <?= $pourcentage ?>%; background-color: var(--color-accent);"></div>
    </div>
    <ol class="d-flex justify-content-between list-unstyled mt-2 mb-0 small">
        <?php foreach ($labels as $i => $label): ?>
            <li class="<?= $i === $step ? 'fw-bold text-dark' : ($i < $step ? 'text-success' : 'text-muted') ?>"
                <?= $i === $step ? 'aria-current="step"' : '' ?>>
                <?= $i ?>. <?= $label ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>