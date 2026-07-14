<?php 


// demande de lien de reinitialisation de mot de passe

/** @var array $errors */
render_standard_field('email', 'Votre adresse mail', 'email', 'email@exemple.com', $_POST['email'] ?? '', 'error_email', $errors ?? []);
?>
<main>
    <div class="row justify-content-center my-5">
        <div class="col-md-5 p-4 bg-white shadow-sm rounded">
            <h2 class="text-center mb-4">Réinitialisation</h2>
            <p class="text-muted text-center">Entrez votre adresse mail pour recevoir un lien de réinitialisation.</p>
            
            <form action="index.php?page=password-forgotten-send" method="POST">
                <?php 
                render_standard_field('email', 'Votre adresse mail', 'email', 'email@exemple.com', '', 'error_email');
                ?>
                <button type="submit" class="btn btn-primary w-100">Envoyer le lien</button>
            </form>
        </div>
    </div>
</main>