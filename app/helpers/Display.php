<?php

/**
 * Fonction pour générer une ligne de commande
 * @param array $order
 */
function renderOrderRow(array $order)
{
    $badgeClass = match ($order['statut']) {
        'en_attente' => 'bg-warning',                    // jaune - en attente
        'acceptee', 'en_preparation' => 'bg-info',      // bleu - préparation
        'en_cours_livraison' => 'bg-primary',            // bleu foncé - en route
        'livree' => 'bg-success',                        // vert - livré
        'en_attente_retour_materiel' => 'bg-warning',   // jaune - en attente retour
        'terminee' => 'bg-success',                      // vert - terminé
        'annulee' => 'bg-danger',                        // rouge - annulé
        default => 'bg-secondary'
    };
    $statutLabel = [
        'en_attente' => 'En attente',
        'acceptee' => 'Acceptée',
        'en_preparation' => 'En préparation',
        'en_cours_livraison' => 'En cours de livraison',
        'livree' => 'Livrée',
        'en_attente_retour_materiel' => 'En attente du retour de matériel',
        'terminee' => 'Terminée',
        'annulee' => 'Annulée'
    ][$order['statut']] ?? htmlspecialchars($order['statut']);

    $modalId = 'orderModal' . $order['commande_id'];
?>
<li class="list-group-item d-flex justify-content-between align-items-center">
    <div>
        <strong>Commande n°<?= htmlspecialchars($order['commande_id']) ?></strong><br>
        <strong><?= htmlspecialchars($order['menu_titre'] ?? 'Menu inconnu') ?></strong>
        <small class="text-muted">
            Date prestation : <?= date('d/m/Y', strtotime($order['date_prestation'])) ?>
        </small>
    </div>
    <div>
        <!-- Bouton qui ouvre la modale spécifique à cette commande -->
        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
            Détails
        </button>

        <span class="badge <?= $badgeClass ?> ms-2"><?= $statutLabel ?></span>
        
        <?php if ($order['statut'] === 'en_attente'): ?>
            <button type="button" class="btn btn-sm btn-outline-danger ms-2 btn-erase-order" data-commande-id="<?= $order['commande_id'] ?>">Annuler</button>
            <a href="?page=edit-order&commande_id=<?= $order['commande_id'] ?>" class="btn btn-sm btn-outline-primary ms-2">Modifier</a>
        <?php endif; ?>
    </div>
</li>

<!-- Modale générée par PHP pour cette commande précise -->
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails commande n°<?= htmlspecialchars($order['commande_id']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Menu :</strong> <?= htmlspecialchars($order['menu_titre']) ?></p>
                <p><strong>Date :</strong> <?= date('d/m/Y', strtotime($order['date_prestation'])) ?> à <?= htmlspecialchars($order['heure_livraison']) ?></p>
                <p><strong>Lieu :</strong> <?= htmlspecialchars($order['adresse'] . ', ' . $order['ville']) ?></p>
                <hr>
                <p><strong>Matériel prêté :</strong> <?= $order['pret_materiel'] ? 'Oui' : 'Non' ?></p>
                <p><strong>Dépôt de garantie :</strong> <?= number_format($order['depot_garantie'], 2) ?> €</p>
            </div>
        </div>
    </div>
</div>
<?php
}