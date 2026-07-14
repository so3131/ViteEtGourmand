<?php
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoRide | Moderation</title>

    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../public/assets/css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="../public/assets/css/variables.css">
    <link rel="stylesheet" href="../public/assets/css/dashboard.css">
    <link rel="stylesheet" href="../public/assets/css/employe.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>EcoRide <span>Employé</span></h2>
        </div>

        <nav class="sidebar-menu">
            <ul>
                <li>
                    <a href="index.php?page=employee-dashboard" class="<?php echo $current_page === 'employee-dashboard' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-house"></i> Accueil
                    </a>
                </li>
                <li>
                    <a href="index.php?page=employee-moderation" class="<?php echo $current_page === 'employee-moderation' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-user-check"></i> Moderation des avis
                    </a>
                </li>
                <li>
                    <a href="index.php?page=employee-conflicts" class="<?php echo $current_page === 'employee-conflicts' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-triangle-exclamation"></i> Gestion des conflits
                    </a>
                </li>
                <li>
                    <a aria-current="page" href="?page=home" class="<?php echo $current_page === 'employee-conflicts' ? 'active' : ''; ?>">
                        <i class="fa-solid fa-globe"></i> Acces au site Ecoride
                    </a>
                </li>

            </ul>
        </nav>

        <a href="index.php?action=logout" class="logout-btn">
            <i class="fas fa-power-off"></i> Déconnexion
        </a>
    </aside>

    <main class="main-content">

        <header class="top-bar">
            <div class="breadcrumb">
                <span style="color: var(--color-text-lighter)">Employé /</span> <?php echo ($current_page === 'index') ? 'Accueil' : ''; ?> <?php echo ($current_page === 'employe_moderation') ? 'Moderation des avis' : ''; ?> <?php echo ($current_page === 'employe_conflits') ? 'Gestion des conflits' : ''; ?>
            </div>
            <div class="employe-profile">
                <span style="margin-right: var(--spacing-sm)">Bienvenue, <strong>"Employé"</strong></span>
                <i class="fas fa-user-circle fa-lg" style="color: var(--color-primary)"></i>
            </div>
        </header>

        <section class="content-body">