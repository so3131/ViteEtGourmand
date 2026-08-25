<?php if (!isset($commande, $menuData, $lieux) || !is_array($commande)) : ?>
    <div class="container mt-5">
        <div class="alert alert-danger">Commande introuvable.</div>
    </div>
<?php else : ?>
    <div class="container mt-5">
        <h2>Modifier la commande #<?= $commande['commande_id'] ?></h2>
    
   <form id="updateOrderForm"
      action="index.php?page=update-order-common&commande_id=<?= (int)$commande['commande_id'] ?>"
      method="POST"
      data-id="<?= (int)$commande['commande_id'] ?>"
      class="p-4 border rounded shadow-sm">
        
        <!-- On transmet l'ID de la commande en champ caché pour le traitement POST -->
        <input type="hidden" name="commande_id" value="<?= $commande['commande_id'] ?>">
        <?php if (isset($_SESSION['role_id']) && in_array((int)$_SESSION['role_id'], [ROLE_ADMIN, ROLE_EMPLOYE])): ?>
  
    <div class="card border-warning mb-3">
        <div class="card-header bg-warning text-dark fw-bold">
            <i class="fa-solid fa-shield-halved"></i> Validation Staff requise
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Mode de contact utilisé <span class="text-danger">*</span></label>
                <select name="mode_contact" class="form-select" required>
                    <option value="" disabled selected>-- Choisir --</option>
                    <option value="tel">Appel GSM</option>
                    <option value="mail">Email</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Motif de la modification <span class="text-danger">*</span></label>
                <textarea name="motif" class="form-control" rows="3" required placeholder="Expliquez pourquoi..."></textarea>
            </div>
        </div>
    </div>
<?php endif; ?>
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
            
            <select name="lieu_prestation_id" id="lieu_select" class="form-control">
                <?php foreach ($lieux as $lieu): ?>
                    <option value="<?= $lieu['id'] ?>" <?= ($lieu['id'] == $commande['lieu_prestation_id']) ? 'selected' : '' ?>>
                        <?= $lieu['adresse'] ?> - <?= $lieu['ville'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

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
        <a href="index.php?page=order-management" class="btn btn-secondary">Annuler</a>
    </form>
    </div>
<?php endif; ?>