<!-- Step 0 sur 4: Récupération des infos client, date, lieu et frais de livraison. -->
<?php
/** @var int $menuID */
/** @var string $nom */
/** @var string $prenom */
?>
<?php
$delaiCommande = (int)($menuInfo['delai_commande'] ?? 0);
$dateMinimale = null;

if ($delaiCommande > 0) {
    $dateMinimale = (new DateTime('today'))->modify('+' . $delaiCommande . ' days')->format('Y-m-d');
}
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
    <h2>Vous avez choisis le menu : <?= htmlspecialchars($menuInfo['titre'] ?? 'Menu inconnu') ?></h2>

    <div class="d-flex justify-content-between mb-4 bg-light p-3 rounded">
        <span class="badge bg-primary">0. Détails</span>
        <span class="badge bg-secondary">1. Quantité</span>
        <span class="badge bg-secondary">2. Options</span>
        <span class="badge bg-secondary">3. Récap</span>
    </div>

    <div class="card p-4 shadow-sm">
        <form action="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=0" method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Votre nom</label>
                    <input type="text" name="nom" class="form-control" disabled required
                        value="<?= $_SESSION['nom'] ?? '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Votre prénom</label>
                    <input type="text" name="prenom" class="form-control" disabled required
                        value="<?= $_SESSION['prenom'] ?? '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Votre Email</label>
                    <input type="email" name="email" class="form-control" disabled required
                        value="<?= $_SESSION['email'] ?? '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Votre GSM</label>
                    <input type="text" name="gsm" class="form-control" disabled required
                        value="<?= $_SESSION['telephone'] ?? '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date de prestation</label>
                    <input type="date" name="date_prestation" class="form-control" required
                        min="<?= htmlspecialchars($dateMinimale ?? '') ?>"
                        value="<?= $_SESSION['current_order']['prestation']['date_prestation'] ?? '' ?>">
                    <?php if ($dateMinimale): ?>
                        <small class="text-muted d-block mt-1">
                            Date minimale autorisée : <?= htmlspecialchars(date('d/m/Y', strtotime($dateMinimale))) ?>
                            (<?= $delaiCommande ?> jour<?= $delaiCommande > 1 ? 's' : '' ?> après la commande)
                        </small>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Heure de livraison</label>
                    <input type="time" name="heure_livraison" class="form-control" required
                        value="<?= $_SESSION['current_order']['prestation']['heure_livraison'] ?? '' ?>">
                </div>

                <div class="mb-3">
                    <label for="lieu_id" class="form-label">Ville de livraison</label>
                    <select name="lieu_id" id="lieu_id" class="form-select" required>
                        <option value="">-- Sélectionnez une ville --</option>
                        <?php
                        global $db;
                        $tousLesLieux = \App\Managers\LieuManager::getAll($db);

                        // On récupère l'ID en session pour le test
                        $lieuSessionId = $_SESSION['current_order']['prestation']['lieu']['id'] ?? null;

                        foreach ($tousLesLieux as $lieu):
                            // On ajoute l'attribut "selected" si l'ID correspond
                            $isSelected = ($lieu['id'] == $lieuSessionId) ? 'selected' : '';
                        ?>
                            <option value="<?= $lieu['id'] ?>" data-km="<?= $lieu['distance_bordeaux'] ?>" <?= $isSelected ?>>
                                <?= htmlspecialchars($lieu['ville']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="mb-3 p-3 bg-light border rounded">
                        <strong>Frais de livraison estimés : </strong>
                        <span id="affichage_frais"><?= htmlspecialchars(number_format((float)($_SESSION['current_order']['prestation']['frais_livraison'] ?? 0), 2, '.', '')) ?></span> €
                        <small class="text-muted d-block mt-1">
                            (Calculé automatiquement selon la distance de votre ville par rapport à Bordeaux)
                        </small>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="adresse_precise" class="form-label">Adresse de livraison</label>
                    <input type="text" name="adresse_precise" id="adresse_precise" class="form-control"
                        placeholder="Ex: 12 rue de la Paix" required
                        value="<?= htmlspecialchars($_SESSION['current_order']['prestation']['adresse_precise'] ?? '') ?>">
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <a href="index.php?page=search" class="btn btn-secondary me-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">Valider et choisir la quantité</button>
                </div>
            </div>
        </form>
    </div>
</div>