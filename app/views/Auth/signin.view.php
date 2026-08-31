<?php

/** @var array $errors */
$errors = $errors ?? [];
require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once ROOT_PATH . '/app/helpers/FormHelper.php';
?>


<?php if (isset($errors['general']) || isset($errors['email'])): ?>
    <div class="alert alert-danger">
        <?= $errors['general'] ?? $errors['email'] ?>
    </div>
<?php endif; ?>

<main class="container">
    <div class="row justify-content-center my-4">
        <div class="col-6 bg-white p-4 shadow-sm rounded">
<form class="g-3" method="POST" action="index.php?page=signin">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <h2 class="text-center mb-4">Inscription</h2>

    <div class="row">
        <div class="col-md-6"><?php render_standard_field('nom', 'Nom', 'text', 'Nom', $_POST['nom'] ?? '', 'error_nom', $errors); ?></div>
        <div class="col-md-6"><?php render_standard_field('prenom', 'Prénom', 'text', 'Prénom', $_POST['prenom'] ?? '', 'error_prenom', $errors); ?></div>
    </div>

    <?php render_standard_field('gsm', 'Numéro de GSM', 'tel', '06 00 00 00 00', $_POST['gsm'] ?? '', 'error_gsm', $errors); ?>
    <?php render_standard_field('email', 'Adresse mail', 'email', 'email@exemple.com', $_POST['email'] ?? '', 'error_email', $errors); ?>
    <?php render_standard_field('adresse_postale', 'Adresse postale', 'text', '12 rue de Bordeaux', $_POST['adresse_postale'] ?? '', 'error_adresse', $errors); ?>

    <div class="row">
        <div class="col-md-6"><?php render_standard_field('ville', 'Ville', 'text', 'Bordeaux', $_POST['ville'] ?? '', 'error_ville', $errors); ?></div>
        <div class="col-md-6"><?php render_standard_field('pays', 'Pays', 'text', 'France', $_POST['pays'] ?? '', 'error_pays', $errors); ?></div>
    </div>

    <!-- Les deux champs de mot de passe avec leurs boutons œil -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="input_password" class="form-label fw-bold">Mot de passe</label>
            <div class="input-group">
                <input type="password" class="form-control" id="input_password" name="password" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="input_password">
                    👁️
                </button>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="input_password_confirm" class="form-label fw-bold">Confirmation</label>
            <div class="input-group">
                <input type="password" class="form-control" id="input_password_confirm" name="password_confirm" placeholder="••••••••" required>
                <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="input_password_confirm">
                    👁️
                </button>
            </div>
        </div>
    </div>

    <div class="mt-3 text-center">
        <div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="rgpd_cgv_consent" id="rgpdCgvCheck" required>
    <label class="form-check-label small" for="rgpdCgvCheck">
        J'accepte les <a href="index.php?page=mention#cgv" target="_blank" class="text-decoration-underline">Conditions Générales de Vente</a> et la politique de traitement de mes données personnelles.
    </label>
</div>
        <button type="submit" class="btn btn-primary d-block w-100 mb-2">S'inscrire</button>
        <a href="?page=login" class="text-decoration-none">Déjà membre ? Se connecter</a>
    </div>
</form>
        </div>
    </div>
</main>