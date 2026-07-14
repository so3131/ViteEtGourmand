<div class="container mt-5">
    <h2>Modifier ma commande #<?= $commande['commande_id'] ?></h2>
    
    <form id="updateOrderForm" data-id="<?= $commande['commande_id'] ?>" class="p-4 border rounded shadow-sm">
        
        <div class="mb-3">
            <label>Date de prestation :</label>
            <input type="date" 
       name="date_prestation" 
       min="<?= date('Y-m-d', strtotime('+' . $menuData['delai_commande'] . ' days')) ?>" 
       value="<?= htmlspecialchars($commande['date_prestation']) ?>" 
       required>
        </div>

        <div class="mb-3">
            <label>Heure de livraison :</label>
            <input type="time" name="heure_livraison" class="form-control" value="<?= $commande['heure_livraison'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Nombre de personnes :</label>
            <input type="number" name="nombre_personne" class="form-control" value="<?= $commande['nombre_personne'] ?>" required>
        </div>

        <div class="mb-3">
            <label>Lieu de prestation :</label>
            <!-- On garde l'ID pour le JS, mais on n'a plus besoin des attributs data-prix -->
            <select name="lieu_prestation_id" id="lieu_select" class="form-control">
                <?php foreach ($lieux as $lieu): ?>
                    <option value="<?= $lieu['id'] ?>" <?= ($lieu['id'] == $commande['lieu_prestation_id']) ? 'selected' : '' ?>>
                        <?= $lieu['adresse'] ?> - <?= $lieu['ville'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Ce hidden est CRUCIAL pour le contrôleur -->
        <input type="hidden" name="menu_id" value="<?= $commande['menu_id'] ?>">

        <div class="form-check mb-3">
            <input type="checkbox" name="pret_materiel" value="1" class="form-check-input" id="pret_materiel" <?= $commande['pret_materiel'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="pret_materiel">Prêt de matériel (Caution 600€)</label>
        </div>

        <div class="alert alert-info">
            <strong>Nouveau total estimé : </strong>
            <span id="total-display"><?= number_format($commande['prix_total'], 2) ?> €</span>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="index.php?page=dashboard-user" class="btn btn-secondary">Annuler</a>
    </form>
</div>