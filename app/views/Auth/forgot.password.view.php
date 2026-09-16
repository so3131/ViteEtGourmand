<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <h2 class="h4 fw-bold text-dark">Mot de passe oublié ?</h2>
                        <p class="text-muted small">Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
                    </div>

                    <!-- Message de succès (si défini dans le contrôleur) -->
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Message d'erreur global ou spécifique email -->
                    <?php if (!empty($errors['email'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($errors['email']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=forgot-password">
                        <input type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Adresse email</label>
                            <input type="email" name="email" id="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" placeholder="nom@exemple.com" required>
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
        </div>
    </div>
</div>