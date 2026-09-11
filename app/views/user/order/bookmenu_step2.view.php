 <?php
/** @var int $menuID */
/** @var int $step */
?>
<!-- Step 2 : Options (matériel, etc.). -->
<div class="container mt-4">

<div class="d-flex justify-content-between mb-4 bg-light p-3 rounded">
    <span class="badge <?= ($step == 0) ? 'bg-primary' : 'bg-secondary' ?>">0. Détails</span>
    <span class="badge <?= ($step == 1) ? 'bg-primary' : 'bg-secondary' ?>">1. Quantité</span>
    <span class="badge <?= ($step == 2) ? 'bg-primary' : 'bg-secondary' ?>">2. Options</span>
    <span class="badge <?= ($step == 3) ? 'bg-primary' : 'bg-secondary' ?>">3. Récap</span>
</div>
<div class="container mt-4">
    <div class="card p-4 shadow-sm">
        <form action="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=2" method="POST">
          <input type="hidden"
       name="csrf_token"
       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">  
 <div class="mb-3">
    <label class="form-label">Souhaitez-vous louer du matériel de service ?</label>

    <div class="form-check">
        <input class="form-check-input" type="radio" name="location_materiel" value="1" id="loc_oui"
               <?= (isset($_SESSION['current_order']['prestation']['location_materiel']) && $_SESSION['current_order']['prestation']['location_materiel'] == 1) ? 'checked' : '' ?>>
        <label class="form-check-label" for="loc_oui">Oui (+600€ de dépôt)</label>
    </div>

    <div class="form-check">
        <input class="form-check-input" type="radio" name="location_materiel" value="0" id="loc_non"
               <?= (!isset($_SESSION['current_order']['prestation']['location_materiel']) || $_SESSION['current_order']['prestation']['location_materiel'] == 0) ? 'checked' : '' ?>>
        <label class="form-check-label" for="loc_non">Non</label>
    </div>
</div>

<div class="mb-3" id="conditions-container" style="display: none;">
    <label class="form-label">Conditions de retour</label>
    <div class="p-3 border rounded bg-light mb-2" style="font-size: 0.9em;">
        Le matériel doit être rendu propre et complet sous 10 jours ouvrés. 
        Toute casse ou perte sera facturée selon le barème en vigueur.
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="accept_conditions" id="accept_conditions">
        <label class="form-check-label" for="accept_conditions">J'accepte les conditions de retour du matériel.</label>
    </div>
</div>

            <div class="d-flex justify-content-between mt-4">
                <a href="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=1" class="btn btn-outline-secondary">Retour</a>
                <button type="submit" class="btn btn-primary">Passer à l'étape suivante</button>
            </div>
        </form>
    </div>
</div>
