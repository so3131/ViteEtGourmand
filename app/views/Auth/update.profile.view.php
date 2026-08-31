
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Modifier mes informations personnelles</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?page=update-profil" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <div class="row g-3">
                    <!-- Prénom -->
                    <div class="col-md-6">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" value="<?= htmlspecialchars($_SESSION['prenom'] ?? '') ?>" name="prenom">
                    </div>

                    <!-- Nom -->
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" value="<?= htmlspecialchars($_SESSION['nom'] ?? '') ?>" name="nom">
                    </div>

                    <!-- Email (Non modifiable) -->
                    <div class="col-md-12">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" disabled>
                    </div>

                    <!-- Téléphone (GSM) -->
                    <div class="col-md-6">
                        <label for="numero_tel" class="form-label">Numéro de GSM</label>
                        <input type="tel" class="form-control" id="numero_tel" value="<?= htmlspecialchars($_SESSION['telephone'] ?? '') ?>" name="numero_tel">
                    </div>

                    <!-- Adresse postale -->
                    <div class="col-md-6">
                        <label for="adresse" class="form-label">Adresse postale</label>
                        <input type="text" class="form-control" id="adresse" value="<?= htmlspecialchars($_SESSION['adresse_postale'] ?? '') ?>" name="adresse">
                    </div>

                    <!-- Ville -->
                    <div class="col-md-6">
                        <label for="ville" class="form-label">Ville</label>
                        <input type="text" class="form-control" id="ville" value="<?= htmlspecialchars($_SESSION['ville'] ?? '') ?>" name="ville">
                    </div>

                    <!-- Pays -->
                    <div class="col-md-6">
                        <label for="pays" class="form-label">Pays</label>
                        <input type="text" class="form-control" id="pays" value="<?= htmlspecialchars($_SESSION['pays'] ?? '') ?>" name="pays">
                    </div>
                </div>

                <div class="mt-4">
                    <a href="index.php?page=dashboard-user" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</main>