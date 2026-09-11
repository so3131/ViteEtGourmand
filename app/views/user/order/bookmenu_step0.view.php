<?php
/** @var int $menuID */
/** @var array $menuInfo */
/** @var array $tousLesLieux */
/** @var string|null $dateMinimale */
/** @var int $delaiCommande */
?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="container mt-4">
    <h1 class="mb-4">Informations de livraison</h1>
    <h2>Vous avez choisi le <?= htmlspecialchars($menuInfo['titre'] ?? 'Menu inconnu') ?></h2>

    <!-- Étapes de commande -->
    <div class="d-flex justify-content-between mb-4 bg-light p-3 rounded">
        <span class="badge bg-primary">0. Détails</span>
        <span class="badge bg-secondary">1. Quantité</span>
        <span class="badge bg-secondary">2. Options</span>
        <span class="badge bg-secondary">3. Récap</span>
    </div>

    <div class="card p-4 shadow-sm">
        <form action="index.php?page=order-menu&menu_id=<?= (int)$menuID ?>&step=0" method="POST" id="form-livraison">
<input type="hidden"
       name="csrf_token"
       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            <div class="row">
                <!-- Informations client -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Votre nom</label>
                    <input type="text" class="form-control" readonly value="<?= htmlspecialchars($_SESSION['nom'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Votre prénom</label>
                    <input type="text" class="form-control" readonly value="<?= htmlspecialchars($_SESSION['prenom'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Votre Email</label>
                    <input type="email" class="form-control" readonly value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Votre GSM</label>
                    <input type="text" class="form-control" readonly value="<?= htmlspecialchars($_SESSION['telephone'] ?? '') ?>">
                </div>

                <!-- Date de prestation -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de prestation <span class="text-danger">*</span></label>
                    <input type="date" name="date_prestation" class="form-control" required
                           min="<?= htmlspecialchars($dateMinimale ?? '') ?>"
                           value="<?= htmlspecialchars($_SESSION['current_order']['prestation']['date_prestation'] ?? '') ?>">
                    <?php if (!empty($dateMinimale)): ?>
                        <small class="text-muted d-block mt-1">
                            Date minimale autorisée : <?= htmlspecialchars(date('d/m/Y', strtotime($dateMinimale))) ?>
                            (<?= $delaiCommande ?> jour<?= $delaiCommande > 1 ? 's' : '' ?> après la commande)
                        </small>
                    <?php endif; ?>
                </div>

                <!-- Heure de livraison -->
                <div class="col-md-6 mb-3">
                    <label for="heure_livraison" class="form-label">Heure de livraison <span class="text-danger">*</span></label>
                    <select name="heure_livraison" id="heure_livraison" class="form-select" required>
                        <option value="" disabled selected>-- Choisissez une heure --</option>
                        <?php 
                        $valeurActuelle = $_SESSION['current_order']['prestation']['heure_livraison'] ?? '';
                        $creneaux = [
                            '09:00', '10:00', '10:30' ,'11:00', '11:30', '12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '15:00', '16:00', '17:00', '17:30', '18:00', '18:30', '19:00'
                        ];
                        foreach ($creneaux as $heure): 
                            $selected = ($valeurActuelle === $heure) ? 'selected' : '';
                        ?>
                            <option value="<?= $heure ?>" <?= $selected ?>><?= $heure ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Adresse de livraison / Recherche -->
                <div class="col-md-12 mb-3 position-relative">
                    <label for="adresse_livraison" class="form-label">Adresse de livraison complète <span class="text-danger">*</span></label>
                    <input type="text" name="adresse_livraison" id="adresse_livraison" class="form-control" 
                           placeholder="Commencez à taper votre adresse..." 
                           value="<?= htmlspecialchars($_SESSION['current_order']['prestation']['adresse_livraison'] ?? '') ?>" autocomplete="off" required>
                    <div class="form-text">Entrez votre adresse pour le calcul automatique des frais de livraison.</div>
                    
                    <!-- Conteneur pour les suggestions d'adresses (Autocomplete) -->
                    <div id="suggestions-adresse" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; display: none;"></div>
                </div>

                <!-- Résultat du calcul en direct -->
                <div class="col-md-12 mb-3" id="info-livraison" style="display: none;">
                    <div class="alert alert-info py-2 mb-0">
                        Frais de livraison estimés : <strong id="montant-frais">0.00</strong> €
                    </div>
                </div>

                <!-- Champs cachés pour stocker la ville, la latitude et la longitude envoyés au contrôleur -->
                <input type="hidden" name="ville" id="ville" required value="<?= htmlspecialchars($_SESSION['current_order']['prestation']['ville'] ?? '') ?>">
                <input type="hidden" name="lat" id="lat" required value="<?= htmlspecialchars($_SESSION['current_order']['prestation']['lat'] ?? '') ?>">
                <input type="hidden" name="lon" id="lon" required value="<?= htmlspecialchars($_SESSION['current_order']['prestation']['lon'] ?? '') ?>">

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-end mt-3">
                    <a href="index.php?page=search" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">Valider et choisir la quantité</button>
                </div>
            </div>
        </form>
    </div>
</div>