<?php
// Récupère la page actuelle pour la classe active du menu
$request_uri = $_SERVER['REQUEST_URI'];
$current_page = basename($request_uri, '.php');
// Enlève les slashes au début et fin
$current_page = $_GET['page'] ?? 'dashboard-admin'; // Si c'est vide ou /, c'est la page d'accueil
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

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/trame.css">
    <link rel="stylesheet" href="../assets/css/AdminEmployee/AdminEmployee.css">
</head>

<body class="gestion-theme">

    <div class="d-flex wrapperstyle min-vh-100">

        <!-- Sidebar native Bootstrap -->
        <aside class="sidebar flex-shrink-0 offcanvas-lg offcanvas-start bg-dark text-white p-3" tabindex="-1" id="adminSidebar">
            <div class="sidebar-header d-flex justify-content-between align-items-center mb-4">
                <h2 class="h5 m-0">Vite & Gourmand <span class="text-warning">ADMIN</span></h2>
                <button type="button" class="btn-close btn-close-white d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close"></button>
            </div>

            <nav class="sidebar-menu flex-grow-1">
                <ul class="nav flex-column gap-1">
                    <li class="nav-item">
                        <a href="?page=dashboard-admin" class="nav-link text-white <?= $current_page === 'dashboard-admin' ? 'active bg-primary' : ''; ?>">
                            <i class="fa-solid fa-house me-2"></i> Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=order-management" class="nav-link text-white <?= $current_page === 'order-management' ? 'active bg-primary' : ''; ?>">
                            <i class="fa-solid fa-list-check me-2"></i> Gestion Commandes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=menu-management" class="nav-link text-white <?= $current_page === 'menu-management' ? 'active bg-primary' : ''; ?>">
                            <i class="fa-solid fa-utensils me-2"></i> Menus, Plats & Horaires
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=review-management" class="nav-link text-white <?= $current_page === 'review-management' ? 'active bg-primary' : ''; ?>">
                            <i class="fa-solid fa-user-check me-2"></i> Modération des avis
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=rh-admin" class="nav-link text-white <?= $current_page === 'rh-admin' ? 'active bg-primary' : ''; ?>">
                            <i class="fa-solid fa-user-gear me-2"></i> Gestion Employés
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="?page=stats-admin" class="nav-link text-white <?= $current_page === 'stats-admin' ? 'active bg-primary' : ''; ?>">
                            <i class="fas fa-chart-pie me-2"></i> Rapports & CA
                        </a>
                    </li>
                    <li class="nav-item mt-3 pt-3 border-top border-secondary">
                        <a href="?page=home" class="nav-link text-white">
                            <i class="fa-solid fa-globe me-2"></i> Voir le site
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="mt-auto pt-3">
                <a href="?page=logout" class="btn btn-outline-danger w-100">
                    <i class="fas fa-power-off me-2"></i> Déconnexion
                </a>
            </div>
        </aside>
        <main class="main-content flex-grow-1 w-100">
            <header class="top-bar navbar navbar-expand bg-white border-bottom px-4 py-3">
                <?php
                $pageTitles = require ROOT_PATH . '/app/Config/Titles.php';
                $pageName = $pageTitles[$current_page] ?? ucfirst(str_replace(['-', '_'], ' ', $current_page));
                ?>

                <div class="container-fluid p-0">
                    <div class="d-flex align-items-center">
                        <!-- Bouton Burger affiché uniquement sur mobile/tablette -->
                        <button class="btn btn-outline-secondary d-lg-none me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div class="breadcrumb m-0">
                            <span>Administration / </span>
                            <strong class="ms-1"><?= htmlspecialchars($pageName) ?></strong>
                        </div>
                    </div>

                    <div class="admin-profile d-flex align-items-center">
                        <span class="me-2 d-none d-sm-inline">Bienvenue, <strong>Admin</strong></span>
                        <i class="fas fa-user-circle fa-2x text-secondary"></i>
                    </div>
                </div>
            </header>

            <div class="container-fluid p-4">
                <?php require_once ROOT_PATH . '/app/Views/layout/partials/flash-messages.php'; ?>
                <!-- Vos vues s'insèrent ici -->
            </div>

            <section class="content-body">