<?php

/**
 * Fonction pour générer une ligne de commande
 * @param array $order
 */
function renderOrderRow(array $order)
{
    $badgeClass = match ($order['statut']) {
        'en_attente' => 'bg-warning',                            // jaune - en attente
        'acceptee', 'en_preparation' => 'bg-info',      // bleu - préparation
        'en_cours_livraison' => 'bg-primary',            // bleu foncé - en route
        'livree' => 'bg-success',                        // vert - livré
        'en_attente_retour_materiel' => 'bg-warning',   // jaune - en attente retour
        'terminee' => 'bg-success',                        // vert - terminé
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
    $reviewModalId = 'reviewModal' . $order['commande_id'];
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

        <!-- BOUTON AVIS (Uniquement si la commande est terminée) -->
       <?php
// On vérifie si un avis existe déjà pour cette commande
$reviewManager = new \App\Managers\MongoReviewManager();
$hasReviewed = $reviewManager->alreadyReviewedOrder($order['commande_id']);
?>

<!-- Ton bouton dans la ligne de commande -->
<?php if ($order['statut'] === 'terminee'): ?>
    <?php if ($hasReviewed): ?>
        <span class="badge bg-success">Avis déjà publié</span>
    <?php else: ?>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#<?= $reviewModalId ?>">
            Donner mon avis
        </button>
    <?php endif; ?>
<?php endif; ?>
    </div>
</li>

<!-- Modale de détails générée par PHP pour cette commande précise -->
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

<!-- Modale d'avis générée par PHP pour cette commande précise -->
<?php if ($order['statut'] === 'terminee'): ?>
<div class="modal fade" id="<?= $reviewModalId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="index.php?page=store-review" method="POST">
            
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="commande_id" value="<?= $order['commande_id'] ?>">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laisser un avis (Commande n°<?= htmlspecialchars($order['commande_id']) ?>)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Note (de 1 à 5)</label>
                        <select name="rating" class="form-select" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                            <option value="4">⭐⭐⭐⭐ (4/5)</option>
                            <option value="3">⭐⭐⭐ (3/5)</option>
                            <option value="2">⭐⭐ (2/5)</option>
                            <option value="1">⭐ (1/5)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Votre commentaire</label>
                        <textarea name="comment" class="form-control" rows="4" required placeholder="Partagez votre expérience sur cette prestation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Envoyer l'avis</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php
}