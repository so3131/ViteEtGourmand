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

                <div class="row">
                    <div class="col-md-6"><?php render_standard_field('password', 'Mot de passe', 'password', '••••••••', '', 'error_password', $errors); ?></div>
                    <div class="col-md-6"><?php render_standard_field('password_confirm', 'Confirmation', 'password', '••••••••', '', 'error_password_confirm', $errors); ?></div>
                </div>

                <div class="mt-3 text-center">
                    <div class="form-check d-inline-block mb-3">
                        <input class="form-check-input" type="checkbox" id="invalidCheck2" required>
                        <label class="form-check-label" for="invalidCheck2">J'accepte les conditions</label>
                    </div>
                    <button type="submit" class="btn btn-primary d-block w-100 mb-2">S'inscrire</button>
                    <a href="?page=login" class="text-decoration-none">Déjà membre ? Se connecter</a>
                </div>
            </form>
        </div>
    </div>
</main>