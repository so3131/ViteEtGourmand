<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Modifier mes informations personnelles</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=updateProfil" method="POST">
                <div class="row g-3">
                    <!-- Informations personnelles -->
                    <!-- prenom -->
                    <div class="col-md-6">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" value="<?= htmlspecialchars($_SESSION['prenom'] ?? '') ?>" name="prenom"
                            <?php if (($_SESSION['prenom_modifie'] ?? 0) == 1 && $_SESSION['role_id'] !== 1) echo 'disabled'; ?>>
                        <?php if (($_SESSION['prenom_modifie'] ?? 0) == 1): ?>
                            <small class="text-muted">Contactez le support pour modifier.</small>
                        <?php else: ?>
                            <small class="text-primary">À renseigner une seule fois.</small>
                        <?php endif; ?>
                    </div>
                    <!-- nom -->
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" value="<?= htmlspecialchars($_SESSION['nom'] ?? '') ?>" name="nom"
                            <?php if (($_SESSION['nom_modifie'] ?? 0) == 1 && $_SESSION['role_id'] !== 1) echo 'disabled'; ?>>
                        <?php if (($_SESSION['nom_modifie'] ?? 0) == 1): ?>
                            <small class="text-muted">Contactez le support pour modifier.</small>
                        <?php else: ?>
                            <small class="text-primary">À renseigner une seule fois.</small>
                        <?php endif; ?>
                    </div>
                    <!-- Date de naissance -->
                    <div class="col-md-6">
                        <label for="validationDefault05" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" value="<?= $_SESSION['date_naissance'] ?? '' ?>" id="validationDefault05"
                            name="Date_de_naissance"
                            <?php
                            // Bloqué si : (déjà modifié ET que ce n'est pas un admin)
                            if (($_SESSION['date_naiss_modifiee'] ?? 0) == 1 && $_SESSION['role_id'] !== 1) {
                                echo 'disabled';
                            }
                            ?>>
                        <?php if (($_SESSION['date_naiss_modifiee'] ?? 0) == 1): ?>
                            <small class="text-muted">Contactez le support pour modifier).</small>
                        <?php else: ?>
                            <small class="text-primary">À renseigner une seule fois.</small>
                        <?php endif; ?>
                    </div>
                    <!-- Pseudo -->
                    <div class="col-md-6">
                        <label for="validationDefault04" class="form-label">Pseudo</label>
                        <input type="text" class="form-control" id="validationDefault04" value="<?= htmlspecialchars($_SESSION['pseudo'] ?? '') ?>" name="pseudo" disabled>

                    </div>
                    <!-- email -->
                    <div class="col-md-12">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" name="email" disabled>

                    </div>

                    <!-- Téléphone -->
                    <div class="col-md-6">
                        <label for="numero_tel" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="numero_tel" value="<?= htmlspecialchars($_SESSION['telephone'] ?? '') ?>" name="numero_tel">
                    </div>

                    <!-- Adresse-->
                    <div class="col-md-6">
                        <label for="validationDefault06" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="validationDefault06" value="<?= htmlspecialchars($_SESSION['adresse'] ?? '') ?>" name="adresse">
                    </div>

                    <!-- Ville -->
                    <div class="col-md-6">
                        <label for="ville" class="form-label">Ville</label>
                        <input type="text" class="form-control" id="ville" value="<?= htmlspecialchars($_SESSION['ville'] ?? '') ?>" name="ville">
                    </div>

                    <!-- CODE POSTAL  -->
                    <div class="col-md-6">
                        <label for="validationDefault08" class="form-label">Code Postal</label>
                        <input type="text" class="form-control" id="validationDefault08" value="<?= htmlspecialchars($_SESSION['code_postal'] ?? '') ?>" name="code_postal">
                    </div>

                </div>

                <div class="mt-4">
                    <a href="?page=dashboard" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</main>