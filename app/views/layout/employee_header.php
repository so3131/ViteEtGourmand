<?php
// Récupère la page actuelle pour la classe active du menu
$request_uri = $_SERVER['REQUEST_URI'];
$current_page = basename($request_uri, '.php');
// Enlève les slashes au début et fin
$current_page = $_GET['page'] ?? 'dashboard-employee';// Si c'est vide ou /, c'est la page d'accueil
if ($current_page === '' || $current_page === '/') {
    $current_page = 'index';
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand | Administration</title>
    <?php if (isset($specific_styles)): ?>
        <?php foreach ($specific_styles as $style): ?>
            <link rel="stylesheet" href="<?= $style ?>">
        <?php endforeach; ?>
    <?php endif; ?>


    <?php if (isset($specific_stylesheets)): ?>
        <?php foreach ($specific_stylesheets as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../public/assets/css/variables.css">

    <link rel="stylesheet" href="../public/assets/css/AdminEmployee/AdminEmployee.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <!-- Sidebar -->
<div class="d-flex wrapperstyle">       
     <aside class="sidebar flex-shrink-0">
            <div class="sidebar-header">
                <h2>Vite & Gourmand <span>Employee</span></h2>
            </div>

            <nav class="sidebar-menu">
                <ul class="nav flex-column">
                    <!-- 1. Vue d'ensemble (Dashboard) -->
                    <li class="nav-item">
                        <a href="?page=dashboard-employee" class="nav-link <?php echo $current_page === 'dashboard-employee' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-house me-2"></i> Tableau de bord
                        </a>
                    </li>

                    <!-- 2. Gestion Opérationnelle (Commandes & Menus) -->
                    <li class="nav-item">
                        <a href="?page=order-management" class="nav-link <?php echo $current_page === 'order-management' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-list-check me-2"></i> Gestion Commandes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=menu-management" class="nav-link <?php echo $current_page === 'menu-management' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-utensils me-2"></i> Menus, Plats & Horaires
                        </a>
                    </li>

                    <!-- 3. Modération & Avis -->
                    <li class="nav-item">
                        <a href="?page=review-management" class="nav-link <?php echo $current_page === 'review-management' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-user-check me-2"></i> Modération des avis
                        </a>
                    </li>

                                    

                    <!-- 6. Accès Front-office -->
                    <li class="nav-item">
                        <a href="?page=home" class="nav-link <?php echo $current_page === 'home' ? 'active' : ''; ?>">
                            <i class="fa-solid fa-globe me-2"></i> Voir le site
                        </a>
                    </li>
                </ul>
            </nav>

            <a href="?page=logout" class="logout-btn">
                <i class="fas fa-power-off"></i> Déconnexion
            </a>
        </aside>

        <main class="main-content flex-grow-1">
            <header class="top-bar">
             <?php
    $pageTitles = require ROOT_PATH . '/app/config/titles.php';
    $pageName = $pageTitles[$current_page] ?? ucfirst(str_replace(['-', '_'], ' ', $current_page));
?>

<div class="breadcrumb">
    <span style="color: var(--color-text-lighter)">Administration /</span> 
    <strong><?= htmlspecialchars($pageName) ?></strong>
</div>
                <div class="Employee-profile">
                    <span style="margin-right: var(--spacing-sm)">Bienvenue, <strong>Employee</strong></span>
                    <i class="fas fa-user-circle fa-lg" style="color: var(--color-primary)"></i>
                </div>
            </header>

            <section class="content-body">