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
?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
            <strong>Commande n°<?= htmlspecialchars($order['commande_id']) ?></strong><br>
            <small class="text-muted">
                Date prestation : <?= date('d/m/Y', strtotime($order['date_prestation'])) ?>
            </small>
        </div>
        <div>
            <span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span>
            <?php if ($order['statut'] === 'en_attente'): ?>
                <button type="button" class="btn btn-sm btn-outline-danger ms-2 btn-erase-order" data-commande-id="<?= $order['commande_id'] ?>">Annuler</button>
                <form method="POST" action="?page=edit-order&id=<?= $order['commande_id'] ?>" style="display:inline;">
                    <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Modifier</button>
                </form>


            <?php endif; ?>
        </div>
    </li>
<?php
}
?>