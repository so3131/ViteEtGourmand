<!-- Step 5 : Paiement ok et finalisation. -->
<?php if (!isset($menuInfo)): ?>
    <div class="alert alert-danger">Erreur : Menu non trouvé.</div>
<?php else: ?>
    <h1 class="mb-4">Menu sélectionné : <?= htmlspecialchars($menuInfo['titre']) ?></h1>
<?php endif; ?>

<?php
$menu = $menu ?? null;
$menuID = $menuID ?? '';
$step = $step ?? 0;
?>

<div class="container mt-5 text-center">
    <div class="card p-5 shadow-sm">
        <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
        <h1 class="mt-3">Commande confirmée !</h1>
        <p class="lead">Merci pour votre confiance. Votre commande a bien été enregistrée.</p>

        <div class="mt-4">
            <p>Un e-mail récapitulatif vous a été envoyé.</p>
            <a href="index.php?page=home" class="btn btn-primary">Retour à l'accueil</a>
        </div>
    </div>
</div>