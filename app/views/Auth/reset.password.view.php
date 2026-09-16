<?php

/** @var array $errors */
/** @var string|null $success */
?>
<main class="container">
    <div class="row justify-content-center my-5">
        <div class="col-md-6 col-lg-5 p-4 p-md-5 bg-white shadow-sm rounded-3">

            <div class="text-center mb-4">
                <h2 class="h4 fw-bold text-dark mb-2">Nouveau mot de passe</h2>
                <p class="text-muted small">Veuillez choisir un nouveau mot de passe sécurisé (10 caractères min.).</p>
            </div>

            <!-- Message d'erreur général (ex: token expiré ou invalide) -->
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($errors['general']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="text-center mt-4">
                    <a href="index.php?page=forgot-password" class="btn btn-outline-primary w-100">Demander un nouveau lien</a>
                </div>
            <?php elseif (!empty($success)): ?>
                <!-- Message de succès -->
                <div class="alert alert-success text-center" role="alert">
                    <?= htmlspecialchars($success) ?>
                </div>
                <div class="d-grid gap-2 mt-4">
                    <a href="index.php?page=login" class="btn btn-primary py-2 fw-semibold">Retour à la connexion</a>
                </div>
            <?php else: ?>
                <!-- Formulaire de réinitialisation -->
                <form action="index.php?page=reset-password&token=<?= htmlspecialchars($_GET['token'] ?? '') ?>" method="POST">
                    <input type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">


                    <div class="mb-3">
                        <?php
                        render_standard_field('password', 'Nouveau mot de passe', 'password', '••••••••', '', 'error_password', $errors ?? []);
                        ?>
                    </div>

                    <div class="mb-3">
                        <?php
                        render_standard_field('password_confirm', 'Confirmation du mot de passe', 'password', '••••••••', '', 'error_password_confirm', $errors ?? []);
                        ?>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold">Valider le nouveau mot de passe</button>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </div>
</main>