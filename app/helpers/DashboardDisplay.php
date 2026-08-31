<?php

/**
 * Fonction pour générer une ligne de commande
 * @param array $order
 * @param \PDO $db
 */
function renderOrderRow(array $order, \PDO $db)
{
    $badgeClass = match ($order['statut']) {
        'en_attente' => 'bg-warning',                           // jaune - en attente
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
    
    // Récupération de l'historique des statuts pour cette commande
    $historiqueStatuts = \App\Managers\OrderManager::getOrderHistory($db, $order['commande_id']);
?>
<li class="list-group-item d-flex justify-content-between align-items-center py-3">
    <div class="d-flex align-items-center gap-3">
        <span class="badge <?= $badgeClass ?> fs-6"><?= $statutLabel ?></span>
        <div>
            <strong>Commande n°<?= htmlspecialchars($order['numero_commande']) ?></strong><br>
            <strong><?= htmlspecialchars($order['menu_titre'] ?? 'Menu inconnu') ?></strong>
            <small class="text-muted d-block">
                Date prestation : <?= date('d/m/Y', strtotime($order['date_prestation'])) ?>
            </small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#<?= $modalId ?>">
            Détails & Suivi
        </button>

        <?php if ($order['statut'] === 'en_attente'): ?>
            <a href="?page=edit-order&commande_id=<?= $order['commande_id'] ?>" class="btn btn-sm btn-outline-primary">Modifier</a>
            <button type="button" class="btn btn-sm btn-outline-danger btn-erase-order" data-commande-id="<?= $order['commande_id'] ?>">Annuler</button>
        <?php endif; ?>

        <?php
        $reviewManager = new \App\Managers\MongoReviewManager();
        $hasReviewed = $reviewManager->alreadyReviewedOrder($order['commande_id']);
        ?>

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

<!-- Modale de détails enrichie avec la Chronologie -->
<div class="modal fade" id="<?= $modalId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-receipt me-2"></i>Détails de la commande n°<?= htmlspecialchars($order['numero_commande']) ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Informations générales -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white h-100">
                            <h6 class="text-primary fw-bold mb-3"><i class="fa-solid fa-info-circle me-1"></i> Généralités</h6>
                            <p class="mb-2"><strong>Statut :</strong> <span class="badge <?= $badgeClass ?>"><?= $statutLabel ?></span></p>
                            <p class="mb-2"><strong>Date de commande :</strong> <?= date('d/m/Y', strtotime($order['date_commande'])) ?></p>
                            <p class="mb-2"><strong>Menu choisi :</strong> <?= htmlspecialchars($order['menu_titre'] ?? 'Menu inconnu') ?></p>
                            <p class="mb-0"><strong>Nombre de personnes :</strong> <?= (int)$order['nombre_personne'] ?></p>
                        </div>
                    </div>

                    <!-- Prestation et Livraison -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-white h-100">
                            <h6 class="text-primary fw-bold mb-3"><i class="fa-solid fa-truck me-1"></i> Prestation</h6>
                            <p class="mb-2"><strong>Date de prestation :</strong> <?= date('d/m/Y', strtotime($order['date_prestation'])) ?></p>
                            <p class="mb-2"><strong>Heure de livraison :</strong> <?= htmlspecialchars($order['heure_livraison']) ?></p>
                            <p class="mb-0"><strong>Lieu :</strong> <?= htmlspecialchars($order['adresse'] . ', ' . $order['ville']) ?></p>
                        </div>
                    </div>

                    <!-- Facturation & Matériel -->
                    <div class="col-12">
                        <div class="p-3 border rounded bg-white">
                            <h6 class="text-primary fw-bold mb-3"><i class="fa-solid fa-euro-sign me-1"></i> Facturation & Matériel</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Prêt de matériel :</strong> <?= $order['pret_materiel'] ? 'Oui' : 'Non' ?></p>
                                    <p class="mb-0"><strong>Dépôt de garantie :</strong> <?= number_format($order['depot_garantie'], 2) ?> €</p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <p class="mb-2">Prix du menu : <?= number_format($order['prix_menu'], 2) ?> €</p>
                                    <p class="mb-2">Frais de livraison : <?= number_format($order['prix_livraison'], 2) ?> €</p>
                                    <h5 class="text-success fw-bold mt-2">Total : <?= number_format($order['prix_total'], 2) ?> €</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chronologie des statuts (Timeline ECF) -->
                    <div class="col-12">
                        <div class="p-3 border rounded bg-white">
                            <h6 class="text-primary fw-bold mb-3"><i class="fa-solid fa-clock-rotate-left me-1"></i> Chronologie & Suivi de la commande</h6>
                            <?php if (!empty($historiqueStatuts)): ?>
                                <ul class="list-unstyled ps-2 mb-0 border-start border-primary border-2 ms-2">
                                    <?php foreach ($historiqueStatuts as $hist): ?>
                                        <li class="mb-3 ps-3 position-relative">
                                            <span class="fw-bold text-dark"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $hist['statut']))) ?></span>
                                            <small class="text-muted d-block"><?= date('d/m/Y à H:i', strtotime($hist['date_changement'])) ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted small mb-0">Aucun historique de statut enregistré pour le moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modale d'avis -->
<?php if ($order['statut'] === 'terminee'): ?>
<div class="modal fade" id="<?= $reviewModalId ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="index.php?page=store-review" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="commande_id" value="<?= $order['commande_id'] ?>">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laisser un avis (Commande n°<?= htmlspecialchars($order['numero_commande']) ?>)</h5>
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