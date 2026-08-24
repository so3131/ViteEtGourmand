<div class="card shadow-sm p-4">
    <h3 class="mb-4">Modération des Avis Clients</h3>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap table-sm" id="reviewsTable">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Commande</th>
                    <th>Note</th>
                    <th>Commentaire</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reviews) && is_array($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($review['created_at']))); ?></td>
                            <td><?php echo htmlspecialchars($review['client_nom']); ?></td>
                            <td><?php echo htmlspecialchars($review['menu_titre']); ?></td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <i class="fa-solid fa-star"></i> <?php echo htmlspecialchars($review['note']); ?>/5
                                </span>
                            </td>
                            <td class="text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($review['commentaire']); ?>">
                                <?php echo htmlspecialchars($review['commentaire']); ?>
                            </td>
                            <td>
                                <span class="badge <?php echo ($review['statut'] == 'valide') ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($review['statut'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <?php if ($review['statut'] !== 'valide'): ?>
                                        <!-- Bouton Valider -->
                                        <form action="index.php?page=validate-review" method="POST">
                                            <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Publier l'avis">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <!-- Bouton Supprimer -->
                                    <form action="index.php?page=delete-review" method="POST">
                                        <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer l'avis">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <div class="modal fade" id="contactModal<?= $review['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contacter (nom du client a mettre avec php)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="index.php?page=contact-client" method="POST">
                    <input type="hidden" name="review_id" value="<?= $review['id'] ?>">
                    <textarea name="message" class="form-control" rows="4" placeholder="Votre message au client..." required></textarea>
                    <button type="submit" class="btn btn-primary mt-3 w-100">Envoyer le message</button>
                </form>
            </div>
        </div>
    </div>
</div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">Aucun avis à modérer.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
