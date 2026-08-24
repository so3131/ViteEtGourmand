<?php $menu = $menu ?? []; ?>

<div class="container my-5">
    <div class="card shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Modifier le menu : <?= htmlspecialchars($menu['titre'] ?? '') ?></h3>
            <a href="index.php?page=menu-management" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>

        <form action="index.php?page=update-menu-process&id=<?= $menu['menu_id'] ?>" method="POST">
            <!-- Infos Menu -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Titre du Menu</label>
                    <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($menu['titre'] ?? '') ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Prix/Pers (€)</label>
                    <input type="number" step="0.01" name="prix" class="form-control" value="<?= htmlspecialchars($menu['prix_par_personne'] ?? '') ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Qté Restante</label>
                    <input type="number" name="quantite" class="form-control" value="<?= htmlspecialchars($menu['quantite_restante'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Sélection des plats -->
            <div class="mb-4">
                <label class="form-label">Composer le menu (Plats disponibles)</label>
                <div class="row border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                    <?php 
                    $all_plats = $all_plats ?? []; 
                    // Tableau contenant les IDs des plats déjà associés à ce menu
                    $selected_plats_ids = $selected_plats_ids ?? []; 
                    ?>
                    
                    <?php foreach ($all_plats as $plat): ?>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="plats[]" value="<?= $plat['plat_id'] ?>" id="plat<?= $plat['plat_id'] ?>"
                                    <?= in_array($plat['plat_id'], $selected_plats_ids) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="plat<?= $plat['plat_id'] ?>">
                                    <?= htmlspecialchars($plat['titre_plat']) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?page=menu-management" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>