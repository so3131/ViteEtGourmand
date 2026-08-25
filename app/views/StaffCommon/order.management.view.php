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
        <option value="en_attente" <?php echo (($_GET['status'] ?? '') === 'en_attente') ? 'selected' : ''; ?>>En attente</option>
        <option value="acceptee" <?php echo (($_GET['status'] ?? '') === 'acceptee') ? 'selected' : ''; ?>>Acceptée</option>
        <option value="en_preparation" <?php echo (($_GET['status'] ?? '') === 'en_preparation') ? 'selected' : ''; ?>>En préparation</option>
        <option value="en_cours_livraison" <?php echo (($_GET['status'] ?? '') === 'en_cours_livraison') ? 'selected' : ''; ?>>En cours de livraison</option>
        <option value="livree" <?php echo (($_GET['status'] ?? '') === 'livree') ? 'selected' : ''; ?>>Livrée</option>
        <option value="en_attente_retour_materiel" <?php echo (($_GET['status'] ?? '') === 'en_attente_retour_materiel') ? 'selected' : ''; ?>>En attente retour matériel</option>
        <option value="terminee" <?php echo (($_GET['status'] ?? '') === 'terminee') ? 'selected' : ''; ?>>Terminée</option>
        <option value="annulee" <?php echo (($_GET['status'] ?? '') === 'annulee') ? 'selected' : ''; ?>>Annulée</option>
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
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <!-- 1. Bouton "Voir" (Ouvre la modale de détails) -->
        <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $order['commande_id']; ?>" title="Voir les détails">
            <i class="fa-solid fa-eye"></i> Voir
        </button>

        <!-- 2. Bouton "Modification" (Page dédiée) -->
        <a href="index.php?page=edit-order-common&commande_id=<?php echo urlencode($order['commande_id']); ?>"
            class="btn btn-sm btn-warning" title="Modifier">Modif.</a>

      <!-- 3. Formulaire de changement rapide de statut -->
        <form action="index.php?page=update-order-status" method="POST" class="d-inline m-0">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="commande_id" value="<?= $order['commande_id'] ?>">
            
            <select name="nouveau_statut" class="form-select form-select-sm d-inline-block" style="width: 140px;" onchange="this.form.submit()">
                <option value="en_attente" <?= $order['statut'] === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                <option value="acceptee" <?= $order['statut'] === 'acceptee' ? 'selected' : '' ?>>Acceptée</option>
                <option value="en_preparation" <?= $order['statut'] === 'en_preparation' ? 'selected' : '' ?>>En préparation</option>
                <option value="en_cours_livraison" <?= $order['statut'] === 'en_cours_livraison' ? 'selected' : '' ?>>En livraison</option>
                <option value="livree" <?= $order['statut'] === 'livree' ? 'selected' : '' ?>>Livrée</option>
                <option value="en_attente_retour_materiel" <?= $order['statut'] === 'en_attente_retour_materiel' ? 'selected' : '' ?>>Retour matériel</option>
                
                <!-- On n'autorise à passer en "Terminée" que si aucun matériel n'a été prêté OU si le matériel a été restitué -->
                <?php if ($order['pret_materiel'] == 0 || $order['restitution_materiel'] == 1): ?>
                    <option value="terminee" <?= $order['statut'] === 'terminee' ? 'selected' : '' ?>>Terminée</option>
                <?php endif; ?>
            </select>
        </form>

        <!-- Bouton de "Prendre contact" si le matériel est en attente de retour -->
        <?php if ($order['statut'] === 'en_attente_retour_materiel' && $order['pret_materiel'] == 1): ?>
            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#contactModal<?= $order['commande_id'] ?>" title="Prendre contact pour le matériel">
                <i class="fa-solid fa-phone"></i> Contact
            </button>
        <?php endif; ?>

        <!-- 4. Bouton "Annuler" (Déclenche la modale d'annulation) -->
        <?php if ($order['statut'] !== 'annulee'): ?>
            <button type="button" class="btn btn-sm btn-danger"
                data-bs-toggle="modal"
                data-bs-target="#cancelModal<?= $order['commande_id'] ?>" title="Annuler la commande">
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
    <!-- PLACEMENT DES MODALES : TOUTES DANS LA BOUCLE -->
    <?php if (!empty($orders) && is_array($orders)): ?>
        <?php foreach ($orders as $order): ?>

            <!-- 1. Modale Détails -->
            <div class="modal fade" id="modal-<?= $order['commande_id'] ?>" tabindex="-1">
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
                            <p><strong>Date limite de restitution :</strong> <?= date('d/m/Y', strtotime($order['date_limite_restitution'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Modale Annulation -->
            <?php if ($order['statut'] !== 'annulee'): ?>
                <div class="modal fade" id="cancelModal<?= $order['commande_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="index.php?page=cancel-order-common" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
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
                        </form>
                    </div>
                </div>
            <?php endif; ?>

       <!-- 3. Modale Prise de contact Matériel -->
            <?php if ($order['pret_materiel'] == 1 && $order['statut'] === 'en_attente_retour_materiel'): ?>
                <div class="modal fade" id="contactModal<?= $order['commande_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <form action="index.php?page=contact-material-client" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="commande_id" value="<?= $order['commande_id'] ?>">
                            <input type="hidden" name="mode_contact" value="mail">
                            
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">E-mail de relance matériel - Commande n°<?= $order['commande_id'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted small">
                                        Rappel : Si le matériel n'est pas restitué sous 10 jours ouvrés, des frais de 600€ s'appliquent (CGV).
                                    </p>

                                    <!-- Affichage de l'e-mail pré-rempli -->
                                    <div class="mb-3">
                                        <label class="form-label">Destinataire :</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($order['client_email'] ?? $order['email'] ?? 'Non renseigné') ?>" readonly>
                                        </div>
                                    </div>

                                    <label class="form-label">Notes / Compte-rendu de l'e-mail :</label>
                                    <textarea name="commentaire_contact" class="form-control" rows="3" placeholder="Détails de l'e-mail envoyé au client..." required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button type="submit" class="btn btn-primary">Envoyer l'e-mail</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        <?php endforeach; ?>
    <?php endif; ?>