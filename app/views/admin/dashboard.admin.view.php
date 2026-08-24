<?php
/** @var int $todayOrders */
/** @var int $todaySales */
/** @var int $reviewsCount */
?>
<!-- Statistiques rapides -->
<div class="row">
    <!-- Commandes du jour -->
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h3><?= htmlspecialchars($todayOrders) ?></h3>
            <p class="text-muted">Commandes du jour</p>
        </div>
    </div>

    <!-- Chiffre d'affaires du jour -->
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h3><?= htmlspecialchars(number_format($todaySales, 2, ',', ' ')) ?> €</h3>
            <p class="text-muted">Chiffre d'affaires du jour</p>
        </div>
    </div>

    <!-- Avis à modérer  -->
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h3>0</h3> 
            <!-- htmlspecialchars(reviewCount) -->
            <p class="text-muted">Avis à modérer</p>
        </div>
    </div>
</div> 
<!-- Zone de Travail Principale -->
 <div class="card shadow-sm p-4">
  <!-- Section Horaires -->
<div class="row">
    <!-- Colonne de gauche : Le tableau récapitulatif -->
    <div class="col-md-8">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">Horaires actuels de la semaine</h3>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Jour</th>
                            <th>Ouverture</th>
                            <th>Fermeture</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($horairesList)): ?>
                            <?php foreach ($horairesList as $h): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($h['jour']) ?></strong></td>
                                    <td><?= $h['heure_ouverture'] ? htmlspecialchars($h['heure_ouverture']) : '<span class="text-muted">Fermé</span>' ?></td>
                                    <td><?= $h['heure_fermeture'] ? htmlspecialchars($h['heure_fermeture']) : '<span class="text-muted">Fermé</span>' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">Aucun horaire enregistré.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Colonne de droite : Le formulaire de modification -->
    <div class="col-md-4">
        <div class="card shadow-sm p-4 mb-4">
            <h3 class="mb-3">Modifier un jour</h3>
            <form action="?page=dashboard-admin" method="POST">
                <div class="mb-3">
                    <label for="jour" class="form-label">Jour</label>
                    <select name="jour" id="jour" class="form-select" required>
                        <option value="Lundi">Lundi</option>
                        <option value="Mardi">Mardi</option>
                        <option value="Mercredi">Mercredi</option>
                        <option value="Jeudi">Jeudi</option>
                        <option value="Vendredi">Vendredi</option>
                        <option value="Samedi">Samedi</option>
                        <option value="Dimanche">Dimanche</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="heure_ouverture" class="form-label">Heure d'ouverture</label>
                    <input type="time" name="heure_ouverture" id="heure_ouverture" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="heure_fermeture" class="form-label">Heure de fermeture</label>
                    <input type="time" name="heure_fermeture" id="heure_fermeture" class="form-control">
                </div>
                <button type="submit" name="update_horaire" class="btn btn-primary w-100">Enregistrer</button>
            </form>
        </div>
    </div>
</div>

</div>
