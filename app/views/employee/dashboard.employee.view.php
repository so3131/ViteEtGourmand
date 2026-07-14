<!-- Ce fichier est inclus par le controller dashboardEmployeeController.php mais php inteliphense me met une erreur "undefined variable" alors que c'est faux, les variables sont bien définies dans le controller et passées à la vue. C'est un faux positif de l'IDE, il faut juste ignorer l'erreur. -->
<?php
/** @var int $pendingAvis */
/** @var int $pendingSignalements */
?>

<main class="employee-dashboard">
    <header class="dashboard-intro">
        <h1>Espace Employé - EcoRide</h1>
        <p>Gestion de la modération et des litiges</p>
        <p>Bienvenue sur le tableau de bord employé. Utilisez le menu à gauche pour accéder aux différentes fonctionnalités.</p>
    </header>

    <div class="stat-grid">
        <div class="stat-card card-avis">
            <i class="fa-solid fa-comments"></i>
            <div class="stat-info">
                <span class="stat-number"><?php echo htmlspecialchars($pendingAvis); ?></span>
                <span class="stat-label">Avis en attente</span>
            </div>
            <a href="?page=moderation-employee" class="btn-action">Gérer les avis</a>
        </div>

        <div class="stat-card card-incidents">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div class="stat-info">
                <span class="stat-number"><?php echo htmlspecialchars($pendingSignalements); ?></span>
                <span class="stat-label">Incidents signalés</span>
            </div>
            <a href="?page=conflict-employee" class="btn-action">Voir les litiges</a>
        </div>
    </div>
</main>