<?php
$reviews = $reviews ?? [];
?>
<?php foreach ($reviews as $review): ?>
    <div class="card bg-white border-0 shadow-sm mb-2 rounded p-2 px-3">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold"><?= htmlspecialchars($review['nom_auteur'] ?? 'Client') ?></h6>
            <small class="text-muted font-xs">Réf n°:<?= htmlspecialchars($review['numero_commande'] ?? $review['commande_id'] ?? '') ?></small>
        </div>

        <div class="d-flex align-items-center gap-2 my-1">
            <span class="text-warning font-xs"><?= str_repeat('⭐', (int)($review['note'] ?? 0)) ?></span>
            <span class="text-muted font-xs">(<?= $review['note'] ?? 0 ?>/5)</span>
        </div>

        <p class="mb-2 small text-secondary"><?= nl2br(htmlspecialchars($review['description'] ?? '')) ?></p>

        <div class="d-flex justify-content-between align-items-center pt-1 border-top">
            <div class="d-flex flex-column">
                <div class="d-flex align-items-center gap-1">
                    <span class="text-muted font-xs">Statut :</span>
                    <?php
                    $status = $review['statut'] ?? 'pending';
                    $badgeClass = match ($status) {
                        'approved' => 'bg-success',
                        'rejected' => 'bg-danger',
                        default => 'bg-warning text-dark'
                    };
                    ?>
                    <span class="badge <?= $badgeClass ?> font-xs px-2 py-1"><?= htmlspecialchars($status) ?></span>
                </div>

                <!-- Affichage de la traçabilité -->
                <?php if (!empty($review['validated_by_name'])): ?>
                    <small class="text-muted font-xs mt-1">
                        <i class="fa-solid fa-user-check"></i> Traité par <strong><?= htmlspecialchars($review['validated_by_name']) ?></strong>
                        <?php if (isset($review['validated_at'])): ?>

                            <br>le <?= date('d/m/Y à H:i', strtotime($review['validated_at'])) ?>

                        <?php endif; ?>
                    </small>
                <?php endif; ?>
            </div>

            <!-- Formulaires d'action pour Valider ou Refuser -->
            <?php
            $currentStatus = $review['statut'] ?? 'pending';
            $userRole = $_SESSION['role_id'] ?? '';
            $isAdmin = ($userRole === ROLE_ADMIN);
            $isProcessed = ($currentStatus !== 'pending');
            ?>

            <div class="btn-group btn-group-sm" role="group">
                <?php if (!$isAdmin && $isProcessed): ?>
                    <span class="badge bg-secondary font-xs px-2 py-1 align-self-center">
                        <?= $currentStatus === 'approved' ? 'Approuvé (Déjà traité)' : 'Refusé (Déjà traité)' ?>
                    </span>
                <?php else: ?>
                    <!-- Bouton Valider -->
                    <form action="index.php?page=update-review-status" method="POST" class="me-1">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="review_id" value="<?= (int)$review['avis_id'] ?>">
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-xs btn-success <?= $currentStatus === 'approved' ? 'disabled' : '' ?>">
                            Valider
                        </button>
                    </form>

                    <!-- Bouton Refuser -->
                    <form action="index.php?page=update-review-status" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <input type="hidden" name="review_id" value="<?= (int)$review['avis_id'] ?>">
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-xs btn-outline-danger <?= $currentStatus === 'rejected' ? 'disabled' : '' ?>">
                            Refuser
                        </button>
                    </form>

                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>