// demande de reinitialisation de mot de passe

<?php if (isset($errors['general'])): ?>
    <div class="alert alert-danger"><?= $errors['general'] ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
    <a href="index.php?page=login" class="btn btn-primary">Retour à la connexion</a>
<?php else: ?>
    <!-- Ton formulaire ici -->
    <form action="index.php?page=reset-password&token=<?= htmlspecialchars($_GET['token'] ?? '') ?>" method="POST">
        <?php 
        render_standard_field('password', 'Nouveau mot de passe', 'password', '••••••••', '', 'error_password', $errors);
        render_standard_field('password_confirm', 'Confirmation', 'password', '••••••••', '', 'error_password_confirm', $errors);
        ?>
        <button type="submit" class="btn btn-primary w-100">Valider</button>
    </form>
<?php endif; ?>