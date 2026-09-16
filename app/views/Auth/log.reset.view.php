<?php

/** @var array $errors */
/** @var string|null $success */
?>
<main class="container">
    <div class="row justify-content-center my-5">
        <div class="col-md-6 col-lg-5 p-4 p-md-5 bg-white shadow-sm rounded-3">

            <div class="text-center mb-4">
                <h2 class="h4 fw-bold text-dark mb-2">Réinitialisation</h2>
                <p class="text-muted small">Entrez votre adresse mail pour recevoir un lien de réinitialisation.</p>
            </div>

            <!-- Message de succès -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Message d'erreur global (si besoin) -->
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($errors['general']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="index.php?page=forgot-password" method="POST">
                 <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <div class="mb-3">
                   
                    <?php
                    render_standard_field(
                        'email',
                        'Votre adresse mail',
                        'email',
                        'email@exemple.com',
                        $_POST['email'] ?? '',
                        'error_email',
                        $errors ?? []
                    );
                    ?>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold">Envoyer le lien</button>
                </div>
            </form>

            <div class="text-center mt-4">
                <a href="index.php?page=login" class="text-decoration-none small text-muted">&larr; Retour à la connexion</a>
            </div>

        </div>
    </div>
</main>