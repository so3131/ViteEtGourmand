<?php if (!isset($commande, $menuData, $lieux) || !is_array($commande)) : ?>
    <div class="container mt-5">
        <div class="alert alert-danger">Commande introuvable.</div>
    </div>
<?php else : ?>
    <div class="container mt-5">
        <h2>Modifier ma commande #<?= htmlspecialchars($commande['numero_commande']) ?></h2>

        <form id="updateOrderForm" data-id="<?= htmlspecialchars($commande['id'] ?? $commande['commande_id']) ?>" class="p-4 border rounded shadow-sm">
<input type="hidden"
       name="csrf_token"
       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
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
                <input type="number" name="nombre_personne" class="form-control" value="<?= $commande['nombre_personne'] ?>"min="1" required>
            </div>

            <!-- Adresse de livraison / Recherche ou affichage-->
            <div class="col-md-12 mb-3 position-relative">
                <label for="adresse_livraison" class="form-label">Adresse de livraison complète <span class="text-danger">*</span></label>
                <?php
                $adresse = trim((string)($commande['adresse'] ?? ''));
                $ville = trim((string)($commande['ville'] ?? ''));

                $adresseAffichee = implode(', ', array_filter([
                    $adresse,
                    $ville
                ]));
                ?>
                <input type="text" name="adresse_livraison" id="adresse_livraison" class="form-control"
                    placeholder="Commencez à taper votre adresse..."

                    value="<?= htmlspecialchars($adresseAffichee, ENT_QUOTES, 'UTF-8') ?>" autocomplete="off" required>
                <div class="form-text">Entrez votre adresse pour le calcul automatique des frais de livraison.</div>

                <!-- Conteneur pour les suggestions d'adresses (Autocomplete) -->
                <div id="suggestions-adresse" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; display: none;"></div>
            </div>

            <!-- Résultat du calcul en direct -->
            <div class="col-md-12 mb-3" id="info-livraison" style="display: block;">
                <div class="alert alert-info py-2 mb-0">
                    Frais de livraison estimés : <strong id="montant-frais"><?= number_format($commande['frais_livraison'] ?? 0, 2) ?></strong> €
                </div>
            </div>

            <!-- Champs cachés pour stocker la ville, la latitude et la longitude -->
            <input type="hidden" name="ville" id="ville" required value="<?= htmlspecialchars($commande['ville'] ?? '') ?>">
            <input type="hidden" name="code_postal" id="code_postal" required value="<?= htmlspecialchars($commande['code_postal'] ?? '') ?>">
            <input type="hidden" name="lat" id="lat" required value="<?= htmlspecialchars($commande['latitude'] ?? '') ?>">
            <input type="hidden" name="lon" id="lon" required value="<?= htmlspecialchars($commande['longitude'] ?? '') ?>">

          
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
<?php endif; ?>