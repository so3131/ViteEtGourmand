<?php
$reviews = $reviews ?? [];
?>
<?php foreach ($reviews as $review): ?>
    <div class="list-group-item mb-3 shadow-sm rounded p-3">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <h5 class="mb-1"><?= htmlspecialchars($review['author_name'] ?? 'Client') ?></h5>
            <small class="text-muted">Commande n°<?= htmlspecialchars($review['commande_id'] ?? '') ?></small>
        </div>

        <p class="mb-1">
            <strong>Note :</strong> <?= str_repeat('⭐', (int)($review['rating'] ?? 0)) ?> (<?= $review['rating'] ?? 0 ?>/5)
        </p>
        <p class="mb-2"><?= nl2br(htmlspecialchars($review['comment'] ?? '')) ?></p>

        <div class="d-flex justify-content-between align-items-center mt-2">
            <div>
                <strong>Statut actuel :</strong>
                <?php
                $status = $review['status'] ?? 'pending';
                $badgeClass = match ($status) {
                    'approved' => 'bg-success',
                    'rejected' => 'bg-danger',
                    default => 'bg-warning text-dark'
                };
                ?>
                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
            </div>

            <!-- Formulaires d'action pour Valider ou Refuser -->
            <?php

            $currentStatus = $review['status'] ?? 'pending';

            // Récupération du rôle via la session (adapte le nom de la clé si ce n'est pas 'role')
            $userRole = $_SESSION['role_id'] ?? '';
            $isAdmin = ($userRole === ROLE_ADMIN);
            $isProcessed = ($currentStatus !== 'pending');
            ?>

            <div class="btn-group" role="group">
                <!-- Si c'est un employé (et non admin) et que l'avis a déjà été traité -->
                <?php if (!$isAdmin && $isProcessed): ?>
                    <span class="badge bg-secondary align-self-center p-2">
                        <?= $currentStatus === 'approved' ? 'Approuvé (Déjà traité)' : 'Refusé (Déjà traité)' ?>
                    </span>
                <?php else: ?>
                    <!-- Bouton Valider -->
                    <form action="index.php?page=update-review-status" method="POST" class="me-1">
                        <input type="hidden" name="review_id" value="<?= (string)$review['_id'] ?>">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-sm btn-success <?= $currentStatus === 'approved' ? 'disabled' : '' ?>">
                            Valider
                        </button>
                    </form>

                    <!-- Bouton Refuser -->
                    <form action="index.php?page=update-review-status" method="POST">
                        <input type="hidden" name="review_id" value="<?= (string)$review['_id'] ?>">
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-sm btn-outline-danger <?= $currentStatus === 'rejected' ? 'disabled' : '' ?>">
                            Refuser
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>