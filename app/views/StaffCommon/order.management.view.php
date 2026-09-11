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
            <label for="filter-status" class="form-label">Statut</label>
<select name="status" id="filter-status" class="form-select">
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
    <label class="visually-hidden">Filtrer</label>
    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
</div>
<div class="col-md-2">
    <label class="visually-hidden">Réinitialiser</label>
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
                        <?php
                        // Définition des poids du workflow
                        $workflow = [
                            'en_attente' => 1,
                            'acceptee' => 2,
                            'en_preparation' => 3,
                            'en_cours_livraison' => 4,
                            'livree' => 5,
                            'en_attente_retour_materiel' => 6,
                            'terminee' => 7,
                            'annulee' => 0
                        ];
                        $currentWeight = $workflow[$order['statut']] ?? 0;
                        $isLocked = ($order['statut'] === 'terminee' || $order['statut'] === 'annulee');
                        // Annulation autorisée uniquement si non annulé et poids <= 4 (en_cours_livraison)
                        $canCancel = ($order['statut'] !== 'annulee' && $currentWeight <= 4);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['client_nom'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['date_commande'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['numero_commande'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($order['menu_titre'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($order['date_prestation'] ?? 'now')) . ' à ' . substr($order['heure_livraison'] ?? '00:00', 0, 5)); ?></td>
                            <td>
                                <?php
                                $statutLabels = [
                                    'en_attente' => 'En attente',
                                    'acceptee' => 'Acceptée',
                                    'en_preparation' => 'En préparation',
                                    'en_cours_livraison' => 'En livraison',
                                    'livree' => 'Livrée',
                                    'en_attente_retour_materiel' => 'Retour matériel',
                                    'terminee' => 'Terminée',
                                    'annulee' => 'Annulée'
                                ];

                                $badgeClass = match ($order['statut']) {
                                    'annulee' => 'bg-danger',
                                    'terminee', 'livree' => 'bg-success',
                                    'en_attente' => 'bg-warning',
                                    default => 'bg-info'
                                };
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $statutLabels[$order['statut']] ?? htmlspecialchars($order['statut']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($order['prix_total'] ?? 'N/A'); ?> €</td>
                            <?php
                            $dateLimite = !empty($order['date_limite_restitution']) ? new DateTime($order['date_limite_restitution']) : null;
                            $aujourdhui = new DateTime();
                            $estEnRetard = ($dateLimite && $aujourdhui > $dateLimite && $order['restitution_materiel'] == 0);
                            ?>

                            <td class="<?= $estEnRetard ? 'text-danger fw-bold' : '' ?>">
                                <?= $estEnRetard ? 'RETARD' : ($dateLimite ? $dateLimite->format('d/m/Y') : 'N/A') ?>
                            </td>
                            <td>
                              

                                <!-- 3. Formulaire de changement rapide de statut -->
                                <form action="index.php?page=update-order-status" method="POST" class="d-inline m-0">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="commande_id" value="<?= $order['commande_id'] ?>">
<label for="status-<?= $order['commande_id'] ?>" class="visually-hidden">Changer le statut de la commande</label>
                                    <select name="nouveau_statut" id="status-<?= $order['commande_id'] ?>" class="form-select form-select-sm d-inline-block" style="width: 140px;" onchange="this.form.submit()" <?= $isLocked ? 'disabled' : '' ?>>
                                        <?php
                                        $optionsList = [
                                            'en_attente' => 'En attente',
                                            'acceptee' => 'Acceptée',
                                            'en_preparation' => 'En préparation',
                                            'en_cours_livraison' => 'En livraison',
                                            'livree' => 'Livrée',
                                            'en_attente_retour_materiel' => 'Retour matériel',
                                            'terminee' => 'Terminée',
                                            'annulee' => 'Annulée'
                                        ];

                                        foreach ($optionsList as $key => $label):
                                            $optionWeight = $workflow[$key] ?? 0;
                                            $isDisabled = ($optionWeight < $currentWeight && $key !== $order['statut'] && $key !== 'annulee');
                                        ?>
                                            <option value="<?= $key ?>" <?= ($order['statut'] === $key) ? 'selected' : '' ?> <?= $isDisabled ? 'disabled class="text-muted"' : '' ?>>
                                                <?= $label ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    
                                </form>
                                              <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <!-- 1. Bouton "Voir" -->
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modal-<?php echo $order['commande_id']; ?>" title="Voir les détails">
                                        <i class="fa-solid fa-eye"></i> Voir
                                    </button>

                                    <!-- 2. Bouton "Modification" -->
                                                                            <?php if (!$isLocked): ?>
                                        <a href="index.php?page=edit-order-common&commande_id=<?= urlencode($order['commande_id']) ?>"
                                        class="btn btn-sm btn-warning"
                                        title="Modifier">
                                        Modif.
                                    </a>
                                <?php endif; ?>
                                <!-- 4. Bouton "Annuler" conditionné -->
                                <?php if ($canCancel): ?>
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

<!-- PLACEMENT DES MODALES -->
<?php if (!empty($orders) && is_array($orders)): ?>
    <?php foreach ($orders as $order): ?>
        <?php
        $workflow = [
            'en_attente' => 1,
            'acceptee' => 2,
            'en_preparation' => 3,
            'en_cours_livraison' => 4,
            'livree' => 5,
            'en_attente_retour_materiel' => 6,
            'terminee' => 7,
            'annulee' => 0
        ];
        $currentWeight = $workflow[$order['statut']] ?? 0;
        $canCancel = ($order['statut'] !== 'annulee' && $currentWeight <= 4);
        ?>

        <!-- 1. Modale Détails -->
        <div class="modal fade" id="modal-<?= $order['commande_id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Détails commande n°<?= htmlspecialchars($order['numero_commande'] ?? $order['commande_id']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Client :</strong> <?php echo htmlspecialchars(($order['client_prenom'] ?? '') . ' ' . ($order['client_nom'] ?? 'N/A')); ?></p>
                        <p><strong>E-mail :</strong> <?php echo htmlspecialchars($order['client_email'] ?? 'N/A'); ?></p>
                        <p><strong>Menu :</strong> <?php echo htmlspecialchars($order['menu_titre'] ?? 'N/A'); ?></p>
                        <p><strong>Date de prestation :</strong> <?php echo htmlspecialchars(date('d/m/Y', strtotime($order['date_prestation'] ?? 'now')) . ' à ' . substr($order['heure_livraison'] ?? '00:00', 0, 5)); ?></p>
                        <p><strong>Adresse :</strong> <?php echo htmlspecialchars(($order['adresse_prestation'] ?? 'N/A') . ', ' . ($order['ville_prestation'] ?? 'N/A')); ?></p>
                        <p><strong>Quantité :</strong> <?php echo htmlspecialchars($order['nombre_personne'] ?? 'N/A'); ?> personnes</p>
                        <p><strong>Montant Commande :</strong> <?php echo htmlspecialchars($order['prix_total'] ?? $order['prix_menu'] ?? 'N/A'); ?> €</p>
                        <p><strong>Frais de livraison :</strong> <?php echo htmlspecialchars($order['prix_livraison'] ?? '0'); ?> €</p>
                        <p><strong>Prêt Matériel :</strong> <?php echo ($order['pret_materiel'] == 1) ? 'Oui' : 'Non'; ?></p>
                        <p><strong>Caution :</strong> <?php echo htmlspecialchars($order['depot_garantie'] ?? '0'); ?> €</p>
                        <p><strong>Restitution Matériel :</strong> <?php echo ($order['restitution_materiel'] == 1) ? 'Oui' : 'Non'; ?></p>
                        <?php if (!empty($order['date_limite_restitution'])): ?>
                            <p><strong>Date limite de restitution :</strong> <?= date('d/m/Y', strtotime($order['date_limite_restitution'])) ?></p>
                        <?php endif; ?>

                        <div class="mt-4 pt-3 border-top">
                            <h6 class="text-muted mb-3"><i class="fa-solid fa-address-book"></i> Contacter le client</h6>
                            <div class="d-flex gap-2">
                                <?php if (!empty($order['client_email'])): ?>
                                    <a href="mailto:<?= htmlspecialchars($order['client_email']) ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fa-solid fa-envelope"></i> Envoyer un e-mail
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($order['client_telephone'])): ?>
                                    <a href="tel:<?= htmlspecialchars($order['client_telephone']) ?>" class="btn btn-sm btn-outline-success">
                                        <i class="fa-solid fa-phone"></i> Appeler
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Modale Annulation (Générée uniquement si annulable) -->
        <?php if ($canCancel): ?>
            <div class="modal fade" id="cancelModal<?= $order['commande_id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <form action="index.php?page=cancel-order-common" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="commande_id" value="<?= htmlspecialchars($order['commande_id']) ?>">

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Annuler la commande n°<?= htmlspecialchars($order['numero_commande'] ?? $order['commande_id']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                               <label for="mode-contact-<?= $order['commande_id'] ?>" class="form-label">Mode de contact utilisé :</label>
<select name="mode_contact" id="mode-contact-<?= $order['commande_id'] ?>" class="form-select" required>
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

    <?php endforeach; ?>
<?php endif; ?>