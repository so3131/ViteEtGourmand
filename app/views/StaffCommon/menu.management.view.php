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
                                <div class="d-flex gap-2">
                                    <a href="index.php?page=edit-menu&id=<?= $menu['menu_id'] ?>" class="btn btn-sm btn-outline-warning">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                 <form action="index.php?page=delete-menu&menu_id=<?= $menu['menu_id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce menu ?');">
    <!-- Jeton CSRF de sécurité -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    
    <!-- ID de l'élément à supprimer (optionnel si déjà dans l'URL, mais propre de le garder) -->
    <input type="hidden" name="id" value="<?= $menu['menu_id'] ?>">
    
    <button type="submit" class="btn btn-sm btn-outline-danger">
        <i class="fa-solid fa-trash"></i> Supprimer
    </button>
</form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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

        <!-- Sélection des plats -->
        <div class="mb-3">
            <label class="form-label">Composer le menu (Plats disponibles)</label>
            <div class="row" style="max-height: 300px; overflow-y: auto;">
                <?php $all_plats = $all_plats ?? []; ?>
                <?php foreach ($all_plats as $plat): ?>
                    <div class="col-md-6 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="plats[]" value="<?= $plat['plat_id'] ?>" id="plat<?= $plat['plat_id'] ?>">
                            <label class="form-check-label" for="plat<?= $plat['plat_id'] ?>">
                                <?= htmlspecialchars($plat['titre_plat']) ?>
                            </label>
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
     </div>
