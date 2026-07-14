<?php

/** @var int $totalTickets */ ?>
<div class="stat-grid">
    <div class="stat-card">
        <h3><?php echo htmlspecialchars($activeUsers ?? 0); ?></h3>
        <p>Utilisateurs Actifs</p>
    </div>
    <div class="stat-card">
        <h3><?php echo htmlspecialchars($ecoTrips ?? 0); ?></h3>
        <p>Trajets Éco du mois</p>
    </div>
    <div class="stat-card">
        <h3 class="fw-bold text-success"><?php echo htmlspecialchars(number_format($creditVolume ?? 0, 2, ',', ' ')); ?> Credits</h3>
        <p>Volume de Crédits</p>
    </div>
</div>

<div class="work-area" style="background: var(--color-white); ; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm);">
    <h3 style="margin-top: 0; color: var(--color-dark)">Résumé de l'activité</h3>
    <?php if ($totalTickets > 0): ?>
        <div class="stat-card">
            <a href="index.php?page=tickets-admin" class="btn btn-primary position-relative">
                Gérer les tickets de contact
            </a>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo htmlspecialchars($totalTickets); ?>
                <span class="visually-hidden">tickets en attente</span>
            </span>
        </div>
    <?php endif; ?>
    </a>
</div>


<!-- <select name="categorie">
    <option value="Entree">Entrée</option>
    <option value="Plat">Plat</option>
    <option value="Dessert">Dessert</option>
</select> -->