<?php
// Inclusion du fichier de configuration pour les constantes et du helper pour les formulaires
require_once dirname(__DIR__, 2) . '/Config/constants.php';
require_once ROOT_PATH . '/app/Helpers/FormHelper.php'; ?>

<main class="d-flex align-items-center min-vh-75 mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 bg-white p-4 shadow-sm rounded">
                
                <h2 class="text-center mb-4">Connexion</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger py-2 mb-3">
                        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=login">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <?php 
    // Champ email normal
    render_standard_field('email', 'Email', 'email', 'email@exemple.com', $_POST['email'] ?? '', 'error_email');
    ?>

  
    <div class="mb-3">
        <label for="input_password" class="form-label fw-bold">Mot de passe</label>
        <div class="input-group">
            <input type="password" 
                   class="form-control" 
                   id="input_password" 
                   name="password" 
                   placeholder="••••••••" 
                   required>
            <button class="btn btn-outline-secondary toggle-password-btn" type="button" data-target="input_password">
                👁️
            </button>
        </div>
       
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 small">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="gridCheck1" name="rememberMe">
            <label class="form-check-label" for="gridCheck1">Se souvenir de moi</label>
        </div>
        <a href="index.php?page=forgot-password">Oublié ?</a>
    </div>

    <button type="submit" class="btn btn-primary w-100 mb-3">Connexion</button>
    
    <div class="text-center mt-3">
        <a href="index.php?page=forgot-password" class="text-decoration-none small">Mot de passe oublié ?</a>
    </div>

    <div class="text-center small">
        Pas encore de compte ? 
        <a href="index.php?page=signin&redirect=<?= urlencode($_GET['redirect'] ?? '') ?>">
            Créer un compte
        </a>
    </div>
</form>
            </div>
        </div>
    </div>
</main>