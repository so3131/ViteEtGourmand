<?php
require_once dirname(__DIR__, 2) . '/config/constants.php';

// Récupère la page actuelle pour la classe active du menu
$request_uri = $_SERVER['REQUEST_URI'];
$current_page = basename($request_uri, '.php');
// Enlève les slashes au début et fin
$current_page = trim($current_page, '/');
// Si c'est vide ou /, c'est la page d'accueil
if ($current_page === '' || $current_page === '/') {
    $current_page = 'index';
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Contactez Vite&Gourmand - Service Client et Support</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="Vite&Gourmand">
    <meta property="og:description"
        content=" Vite & Gourmand est un traiteur spécialisé dans la création de menus pour les événements. Nous proposons une large gamme de plats adaptés à tous les goûts et régimes alimentaires, avec un service de livraison rapide et fiable. Découvrez nos menus sur mesure pour rendre votre événement inoubliable.">
        
    <meta property="og:type" content="website">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/trame.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.0/nouislider.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <?php if (isset($specific_styles)): ?>
        <?php foreach ($specific_styles as $style): ?>
            <link rel="stylesheet" href="<?= $style ?>">
        <?php endforeach; ?>
    <?php endif; ?>


    <?php if (isset($specific_fonts)): ?>
        <?php foreach ($specific_fonts as $font): ?>
            <link href="<?= $font ?>" rel="stylesheet">
        <?php endforeach; ?>
    <?php endif; ?>

</head>

<header class="container-fluid">
    <nav class="navbar navbar-expand-lg navbar-frosted">
        <div class="container-fluid">
            <!-- Logo avec taille fluide adaptative -->
            <a class="navbar-brand py-0" href="?page=home">
                <img class="Logo img-fluid" src="assets/img/others/Logo.svg" alt="Logo de la société Vite & Gourmand" style="max-width: 250px; height: auto;">
            </a>

            <!-- Bouton Burger mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Liens de navigation -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
               <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center align-lg-items-start text-center text-lg-start">
    <li class="nav-item">
        <a class="nav-link <?php echo ($current_page === 'index') ? 'active' : ''; ?>" aria-current="page" href="?page=home">Accueil</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($current_page === 'search') ? 'active' : ''; ?>" href="?page=search">Accès à tous les menus</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($current_page === 'contact') ? 'active' : ''; ?>" href="?page=contact">Contact</a>
    </li>
    <?php if (isset($_SESSION['user_id'])): ?>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-center justify-content-lg-start gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Mon compte
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2 text-center text-lg-start" style="min-width: 220px;">
                <li class="text-center py-3 border-bottom mb-2">
                    <img src="assets/images/pictureprofil2.png" class="rounded-circle shadow-sm mb-2" style="width: 60px; height: 60px; object-fit: cover;">
                    <div class="small fw-bold text-dark">Espace Personnel</div>
                </li>
                <?php if (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === ROLE_ADMIN): ?>
                    <li><a class="dropdown-item rounded-2 py-2 px-3" href="?page=dashboard-admin">Gestion Admin</a></li>
                <?php elseif (isset($_SESSION['role_id']) && (int)$_SESSION['role_id'] === ROLE_EMPLOYE): ?>
                    <li><a class="dropdown-item rounded-2 py-2 px-3" href="?page=dashboard-employee">Gestion Employé</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item rounded-2 py-2 px-3 <?php echo ($current_page === 'dashboard-user') ? 'active' : ''; ?>" href="?page=dashboard-user">Gérer mon compte</a></li>
                <?php endif; ?>
                <li><hr class="dropdown-divider my-2"></li>
                <li><a class="dropdown-item rounded-2 py-2 px-3 text-danger btn-logout" href="index.php?page=logout">Déconnexion</a></li>
            </ul>
        </li>
    <?php else: ?>
        <li class="nav-item" id="nav-login-item">
            <a class="nav-link px-3 custom-login-btn" href="?page=login">Connexion / Inscription</a>
        </li>
    <?php endif; ?>
</ul>
            </div>
        </div>
    </nav>
</header>