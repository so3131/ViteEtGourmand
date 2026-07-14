<!-- Step 3 : Récapitulatif et validation. -->    
 <?php
/** @var int $menuID */
/** @var \App\Models\Menu $menu */
/** @var int $step */
/** @var array $orderData */


// // Debug temporaire pour voir la structure
// echo '<pre>';
// var_dump($orderData); 
// echo '</pre>';
?>



<div class="container mt-4">

<div class="d-flex justify-content-between mb-4 bg-light p-3 rounded">
    <span class="badge <?= ($step == 0) ? 'bg-primary' : 'bg-secondary' ?>">0. Détails</span>
    <span class="badge <?= ($step == 1) ? 'bg-primary' : 'bg-secondary' ?>">1. Quantité</span>
    <span class="badge <?= ($step == 2) ? 'bg-primary' : 'bg-secondary' ?>">2. Options</span>
    <span class="badge <?= ($step == 3) ? 'bg-primary' : 'bg-secondary' ?>">3. Récap</span>
</div>

 <div class="recapitulatif">
    <h2>Récapitulatif de votre commande</h2>

    <h3>Informations de prestation</h3>
    <p>Date : <?= htmlspecialchars($orderData['prestation']['date_prestation'] ?? 'Non défini') ?></p>
    <p>Heure : <?= htmlspecialchars($orderData['prestation']['heure_livraison'] ?? 'Non défini') ?></p>
    <p>Lieu : <?= htmlspecialchars($orderData['prestation']['lieu']['ville'] ?? 'Non défini') ?></p>

    <h3>Détails du menu</h3>
    <p>Menu : <?= htmlspecialchars($orderData['prestation']['nom_menu'] ?? 'Non défini') ?></p>
    <p>Nombre de personnes : <?= htmlspecialchars($orderData['menu']['quantite'] ?? 0) ?></p>
    <p>Location de matériel : <?= isset($orderData['options']['location_materiel']) && $orderData['options']['location_materiel'] ? 'Oui' : 'Non' ?></p>
    
    <p> Prix unitaire : <?= number_format($orderData['menu']['prix_unitaire'] ?? 0, 2) ?> €</p>

    <h3>Votre commande</h3>
    <p>Prix du menu : <?= number_format($orderData['menu']['prix_menu_total'] ?? 0, 2) ?> €</p>
    <p>Frais de livraison : <?= number_format($orderData['prestation']['frais_livraison'] ?? 0, 2) ?> €</p>
    <p>Dépôt de garantie : <?= number_format($orderData['prestation']['depot_garantie'] ?? 0, 2) ?> €</p>
    <hr>
   <?php if (!empty($Discount)): ?>
    <p>
        <strong>Bonne nouvelle !</strong> Une réduction de 10% a été appliquée 
        car vous avez commandé pour au moins 5 personnes de plus que le minimum requis.
   </p>
<?php endif; ?>
    <strong>Total à payer : <?= number_format($total_general ?? 0, 2) ?> €</strong>

    <form action="index.php?page=order-menu&step=4" method="POST">
        <a href="index.php?page=order-menu&menu_id=<?= htmlspecialchars($menuID) ?>&step=2" class="btn btn-outline-secondary">Retour</a>
        <button type="submit" class="btn btn-primary">Confirmer et payer</button>
    </form>
</div>