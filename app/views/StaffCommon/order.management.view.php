<div class="card shadow-sm p-4">
    <h3 class="mb-4">Gestion des Commandes</h3>

    <!-- Filtres -->
    <form method="GET" action="index.php?page=order-management" class="row g-3 mb-4 align-items-end">
        <input type="hidden" name="page" value="order-management">

        <div class="col-md-4">
            <label class="form-label">Client</label>
            <input type="text" name="client_nom" class="form-control"
                placeholder="Nom du client"
                value="<?php echo htmlspecialchars($_GET['client_nom'] ?? ''); ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label">Statut</label>
            <select name="status" class="form-select">
                <option value="">Tous les statuts</option>
                <option value="en_attente" <?php echo (($_GET['status'] ?? '') === 'en attente') ? 'selected' : ''; ?>>En attente</option>
                <option value="acceptee" <?php echo (($_GET['status'] ?? '') === 'acceptee') ? 'selected' : ''; ?>>Acceptée</option>
                <option value="en_préparation" <?php echo (($_GET['status'] ?? '') === 'en_préparation') ? 'selected' : ''; ?>>En préparation</option>
                <option value="en_cours_livraison" <?php echo (($_GET['status'] ?? '') === 'en_cours_livraison') ? 'selected' : ''; ?>>En cours de livraison</option>
                <option value="livree" <?php echo (($_GET['status'] ?? '') === 'livree') ? 'selected' : ''; ?>>Livrée</option>
                <option value="en_attente_retour_matériel" <?php echo (($_GET['status'] ?? '') === 'en_attente_retour_matériel') ? 'selected' : ''; ?>>En attente retour matériel</option>
                <option value="terminée" <?php echo (($_GET['status'] ?? '') === 'terminée') ? 'selected' : ''; ?>>Terminée</option>
                <option value="annulée" <?php echo (($_GET['status'] ?? '') === 'annulée') ? 'selected' : ''; ?>>Annulée</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filtrer</button>
        </div>
        <div class="col-md-2">
            <a href="index.php?page=order-management" class="btn btn-outline-secondary w-100">Réinitialiser</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle text-nowrap table-sm" id="ordersTable">
            <thead class="table-light">
                <tr>
                    <th>Client</th>
                    <th>Date Commande</th>
                    <th>N° Commande</th>
                    <th>Menu</th>
                    <th>Prestation</th>
                    <th>Statut</th>
                    <th>Total</th>
                    <th>Date Limite Restitution</th>
                    <th>Détails</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders) && is_array($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['client_nom'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['date_commande'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['numero_commande'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['menu_titre'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($order['date_prestation'] ?? 'now')) . ' à ' . substr($order['heure_livraison'] ?? '00:00', 0, 5)); ?></td>
                            <td>
                                <span class="badge <?php echo ($order['statut'] == 'annulee') ? 'bg-danger' : 'bg-info'; ?>">
                                    <?php echo htmlspecialchars($order['statut']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($order['prix_total'] ?? 'N/A'); ?> €</td>
                            <?php
                            $dateLimite = new DateTime($order['date_limite_restitution']);
                            $aujourdhui = new DateTime();
                            $estEnRetard = ($aujourdhui > $dateLimite && $order['restitution_materiel'] == 0);
                            ?>

                            <td class="<?= $estEnRetard ? 'text-danger fw-bold' : '' ?>">
                                <?= $estEnRetard ? 'RETARD' : date('d/m/Y', strtotime($order['date_limite_restitution'])) ?>
                            </td>
                            <td>

                                <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $order['commande_id']; ?>">Voir</button>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <!-- Bouton Modification -->
                                    <a href="index.php?page=edit-order-common&commande_id=<?php echo urlencode($order['commande_id']); ?>"
                                        class="btn btn-sm btn-warning">Modif.</a>

                                    <!-- Bouton Annuler (déclenche la modale) -->
                                    <?php if ($order['statut'] !== 'annulée'): ?>
                                        <button type="button" class="btn btn-sm btn-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#cancelModal<?= $order['commande_id'] ?>">
                                            Annuler
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>


                    <?php endforeach; ?>
               
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    </div>
    <!-- PLACEMENT DES MODALES : UNIQUEMENT ICI, APRÈS LA TABLE -->
    <?php if (!empty($orders) && is_array($orders)): ?>
        <?php foreach ($orders as $order): ?>

            <!-- Modale Détails -->
            <div class="modal fade d-none" id="modal-<?= $order['commande_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                <h5 class="modal-title">Détails commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                        <p><strong>Adresse :</strong> <?php echo htmlspecialchars(($order['adresse_prestation'] ?? 'N/A') . ', ' . ($order['ville_prestation'] ?? 'N/A')); ?></p>
                        <p><strong>Quantité :</strong> <?php echo htmlspecialchars($order['nombre_personne'] ?? 'N/A'); ?> personnes</p>
                        <p><strong>Montant Commande :</strong> <?php echo htmlspecialchars($order['prix_menu'] ?? 'N/A'); ?> €</p>
                        <p><strong>Frais de livraison :</strong> <?php echo htmlspecialchars($order['prix_livraison'] ?? 'N/A'); ?> €</p>
                        <p><strong>Prêt Matériel :</strong> <?php echo ($order['pret_materiel'] == 1) ? 'Oui' : 'Non'; ?></p>
                        <p><strong>Caution :</strong> <?php echo htmlspecialchars($order['depot_garantie'] ?? 'N/A'); ?> €</p>
                        <p><strong>Restitution Matériel :</strong> <?php echo ($order['restitution_materiel'] == 1) ? 'Oui' : 'Non'; ?></p>
                        <p><strong>Date limite de restitution :</strong> <?= date('d/m/Y', strtotime($order['date_limite_restitution'])) ?></p></div>
                    </div>
                </div>
            </div>

            <!-- Modale Annulation -->
            <?php if ($order['statut'] !== 'annulée'): ?>
                <div class="modal fade d-none" id="cancelModal<?= $order['commande_id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <form action="index.php?page=cancel-order-common" method="POST">
            <input type="hidden" name="commande_id" value="<?= $order['commande_id'] ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Annuler la commande n°<?= $order['commande_id'] ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                                <label class="form-label">Mode de contact utilisé :</label>
                                <select name="mode_contact" class="form-select" required>
                                    <option value="tel">Appel GSM</option>
                                    <option value="mail">Email</option>
                                </select>

                                <label class="form-label mt-2">Motif de l'annulation :</label>
                                <textarea name="motif" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                            </div>
                            </div>
                    </div>
                    </form>
                </div>

<?php endif; ?>

<?php endforeach; ?>
<?php endif; ?>