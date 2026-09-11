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
            <!-- Infos Principales -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Titre du Menu</label>
                    <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($menu['titre'] ?? '') ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Prix/Pers (€)</label>
                    <input type="number"
                    step="0.01" name="prix" class="form-control no-spin" value="<?= htmlspecialchars($menu['prix_par_personne'] ?? '') ?>"min="0" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Qté Restante</label>
                    <input type="number" name="quantite" class="form-control" value="<?= htmlspecialchars($menu['quantite_restante'] ?? '') ?>"min="0" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nb Personnes Minimum</label>
                    <input type="number" name="nombre_personne_minimum" class="form-control" value="<?= htmlspecialchars($menu['nombre_personne_minimum'] ?? '') ?>"
                    min="1">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Délai de commande (jours)</label>
                    <input type="number" name="delai_commande" class="form-control" value="<?= htmlspecialchars($menu['delai_commande'] ?? '') ?>"min="0">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Thème</label>
                    <select name="theme_id" class="form-select">
                        <option value="">-- Aucun thème --</option>
                        <?php 
                        $all_themes = $all_themes ?? [];
                        $current_theme_id = $menu['theme_id'] ?? null;
                        foreach ($all_themes as $theme): 
                        ?>
                            <option value="<?= $theme['theme_id'] ?>" <?= ($current_theme_id == $theme['theme_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($theme['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Régime</label>
                    <select name="regime_id" class="form-select">
                        <option value="">-- Aucun régime --</option>
                        <?php 
                        $all_regimes = $all_regimes ?? [];
                        $current_regime_id = $menu['regime_id'] ?? null;
                        foreach ($all_regimes as $regime): 
                        ?>
                            <option value="<?= $regime['regime_id'] ?>" <?= ($current_regime_id == $regime['regime_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($regime['libelle']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description du Menu</label>
                <textarea name="description_menu" class="form-control" rows="2"><?= htmlspecialchars($menu['description_menu'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Conditions de stockage</label>
                <textarea name="conditions_stockage" class="form-control" rows="2"><?= htmlspecialchars($menu['conditions_stockage'] ?? '') ?></textarea>
            </div>

   <!-- Sélection des plats -->
            <div class="mb-4">
                <label class="form-label fw-bold mb-3">Composer le menu (Plats disponibles par catégorie)</label>
                
                <?php 
                $all_plats = $all_plats ?? []; 
                $selected_plats_ids = $selected_plats_ids ?? []; 

                // 1. On regroupe les plats par leur catégorie exacte en DB
                $platsParCategorie = [];
                foreach ($all_plats as $plat) {
                    $cat = trim($plat['categorie'] ?? 'Autre');
                    $platsParCategorie[$cat][] = $plat;
                }

                // 2. On définit l'ordre d'affichage exact basé sur ta base de données ("Entree" sans accent)
                $ordreCategories = ['Entree', 'Plat', 'Dessert'];
                ?>

                <?php foreach ($ordreCategories as $categorie): ?>
                    <?php if (!empty($platsParCategorie[$categorie])): ?>
                        <div class="card mb-3 p-3 bg-light">
                            <h6 class="text-primary fw-bold border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-utensils me-2"></i><?= htmlspecialchars($categorie === 'Entree' ? 'Entrée' : $categorie) ?>
                            </h6>
                            <div class="row">
                                <?php foreach ($platsParCategorie[$categorie] as $plat): ?>
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
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="index.php?page=menu-management" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>