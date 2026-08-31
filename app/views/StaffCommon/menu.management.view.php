<div class="row g-4">
    <!-- Section Menus & Plats -->
    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Gestion des Menus</h3>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                <i class="fa-solid fa-plus"></i> Ajouter un menu
            </button>
        </div>

        <div class="table-responsive">
             <table class="table table-hover align-middle text-nowrap table-sm" id="menusTable">
                <thead class="table-light">
                    <tr>
                        <th>Menu</th>
                        <th>Prix/Pers</th>
                        <th>Plats inclus</th>
                        <th>Qté Restante</th>
                        <th>Statut / Commandes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $menus = $menus ?? []; ?>

                    <?php foreach ($menus as $menu): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($menu['titre']); ?></strong></td>
                            <td><?php echo number_format($menu['prix_par_personne'], 2); ?> €</td>
                            <td>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars($menu['liste_plats'] ?? 'Aucun plat associé'); ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge <?php echo ($menu['quantite_restante'] < 5) ? 'bg-danger' : 'bg-success'; ?>">
                                    <?php echo $menu['quantite_restante']; ?>
                                </span>
                            </td>
                            <td>
                                <!-- Indicateur de liaison et d'état -->
                                <?php if (($menu['nb_commandes'] ?? 0) > 0): ?>
                                    <span class="badge bg-warning text-dark" title="Ce menu possède des commandes liées">
                                        <i class="fa-solid fa-link"></i> <?= $menu['nb_commandes'] ?> cmd(s) en cours
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Libre</span>
                                <?php endif; ?>

                                <?php if (isset($menu['is_active']) && $menu['is_active'] == 0): ?>
                                    <span class="badge bg-dark">Inactif</span>
                                <?php endif; ?>
                            </td>
                      <td>
    <div class="d-flex gap-2 align-items-center">
        <a href="index.php?page=edit-menu&id=<?= $menu['menu_id'] ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
            <i class="fa-solid fa-pen"></i>
        </a>
        
        <?php 
        $isActive = ($menu['is_active'] ?? 1) == 1;
        $hasOrders = ($menu['nb_commandes'] ?? 0) > 0;
        ?>

        <?php if (!$isActive): ?>
            <!-- Bouton pour Activer -->
            <form action="index.php?page=activate-menu&menu_id=<?= $menu['menu_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous réactiver ce menu ?');">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="id" value="<?= $menu['menu_id'] ?>">
                
                <button type="submit" class="btn btn-sm btn-outline-success" title="Activer">
                    <i class="fa-solid fa-check"></i> Activer
                </button>
            </form>
        <?php else: ?>
            <!-- Bouton pour Désactiver ou Supprimer -->
            <?php 
            $actionText = $hasOrders ? 'Désactiver' : 'Supprimer';
            $confirmMsg = $hasOrders 
                ? "Ce menu possède des commandes. Il sera désactivé (soft delete) au lieu d'être supprimé. Continuer ?" 
                : "Voulez-vous vraiment supprimer ce menu ?";
            $btnClass = $hasOrders ? 'btn-outline-secondary' : 'btn-outline-danger';
            $iconClass = $hasOrders ? 'fa-ban' : 'fa-trash';
            ?>

            <form action="index.php?page=delete-menu&menu_id=<?= $menu['menu_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('<?= $confirmMsg ?>');">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="id" value="<?= $menu['menu_id'] ?>">
                
                <button type="submit" class="btn btn-sm <?= $btnClass ?>" title="<?= $actionText ?>">
                    <i class="fa-solid <?= $iconClass ?>"></i> <?= $actionText ?>
                </button>
            </form>
        <?php endif; ?>
    </div>
</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMenuLabel">Ajouter un nouveau menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="index.php?page=add-menu-process" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <div class="modal-body">
                    <!-- Infos Menu -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Titre du Menu</label>
                            <input type="text" name="titre" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Prix / Pers (€)</label>
                            <input type="number" step="0.01" name="prix" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Qté Restante</label>
                            <input type="number" name="quantite" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Minimum de personnes</label>
                            <input type="number" name="min_personne" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Délai (en jours)</label>
                            <input type="number" name="delai" class="form-control" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Conditions particulières</label>
                            <textarea name="conditions" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Sélection et ajout rapide des plats par catégorie -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Composer le menu (Plats disponibles par catégorie)</label>
                        <div style="max-height: 400px; overflow-y: auto;" class="pe-2">
                            <?php 
                            $all_plats = $all_plats ?? []; 
                            $platsParCategorie = [];
                            foreach ($all_plats as $plat) {
                                $cat = trim($plat['categorie'] ?? 'Autre');
                                $platsParCategorie[$cat][] = $plat;
                            }
                            $ordreCategories = ['Entree', 'Plat', 'Dessert'];
                            ?>

                            <?php foreach ($ordreCategories as $categorie): ?>
                                <div class="card mb-3 p-3 bg-light border">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                        <h6 class="text-primary fw-bold m-0">
                                            <i class="fa-solid fa-utensils me-2"></i><?= htmlspecialchars($categorie === 'Entree' ? 'Entrée' : $categorie) ?>
                                        </h6>
                                        <!-- Bouton pour ouvrir le formulaire complet d'ajout de plat -->
                                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="collapse" data-bs-target="#addPlatForm<?= $categorie ?>">
                                            <i class="fa-solid fa-plus"></i> Ajouter un plat
                                        </button>
                                    </div>

                                    <!-- Formulaire complet pour ajouter un plat à cette catégorie -->
                                    <div class="collapse mb-3 p-3 bg-white border rounded shadow-sm" id="addPlatForm<?= $categorie ?>">
                                        <h6 class="text-success mb-2">Nouveau plat - <?= htmlspecialchars($categorie === 'Entree' ? 'Entrée' : $categorie) ?></h6>

                                        <div class="mb-2">
                                            <input type="text" id="titrePlat<?= $categorie ?>" class="form-control form-control-sm" placeholder="Titre du plat *">
                                        </div>

                                        <div class="mb-2">
                                            <textarea id="descPlat<?= $categorie ?>" class="form-control form-control-sm" rows="2" placeholder="Description du plat..."></textarea>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label text-muted small mb-1">Allergènes :</label>
                                            <div class="row g-1" style="max-height: 100px; overflow-y: auto;">
                                                <?php $all_allergenes = $all_allergenes ?? []; ?>
                                                <?php foreach ($all_allergenes as $allergene): ?>
                                                    <div class="col-6">
                                                        <div class="form-check form-check-inline small">
                                                            <input class="form-check-input new-allergene-<?= $categorie ?>" type="checkbox" value="<?= $allergene['allergene_id'] ?>" id="newAllergene<?= $categorie ?>_<?= $allergene['allergene_id'] ?>">
                                                            <label class="form-check-label" for="newAllergene<?= $categorie ?>_<?= $allergene['allergene_id'] ?>">
                                                                <?= htmlspecialchars($allergene['nom_allergene'] ?? $allergene['libelle'] ?? '') ?>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <label class="form-label text-muted small mb-1">Photo :</label>
                                            <input type="file" id="photoPlat<?= $categorie ?>" class="form-control form-control-sm" accept="image/*">
                                        </div>

                                        <button type="button" class="btn btn-success btn-sm w-100 mt-2" onclick="addNewPlat('<?= $categorie ?>')">Enregistrer ce plat</button>
                                    </div>

                                    <!-- Liste des checkboxes des plats existants -->
                                    <div class="row" id="platsContainer<?= $categorie ?>">
                                        <?php if (!empty($platsParCategorie[$categorie])): ?>
                                            <?php foreach ($platsParCategorie[$categorie] as $plat): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="plats[]" value="<?= $plat['plat_id'] ?>" id="modalPlat<?= $plat['plat_id'] ?>">
                                                        <label class="form-check-label" for="modalPlat<?= $plat['plat_id'] ?>">
                                                            <?= htmlspecialchars($plat['titre_plat']) ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12 text-muted small fst-italic">Aucun plat dans cette catégorie.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer le menu</button>
                </div>
            </form>
        </div>
    </div>
</div>