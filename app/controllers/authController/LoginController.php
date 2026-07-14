<?php
namespace App\Controllers\AuthController;
require_once dirname(__DIR__, 2) . '/config/constants.php';

/**
 * Gère la connexion des utilisateurs (Authentification)
 */
Class LoginController
{

public static function LogIn(\PDO $db)
{
    // Initialisation de l'erreur à null pour éviter les "undefined variable" dans la vue
    $error = null;
if (isset($_GET['redirect'])) {
    // On autorise uniquement les redirections vers nos pages internes
    if (strpos($_GET['redirect'], 'index.php?page=order-menu') === 0) {
        $_SESSION['redirect_after_login'] = $_GET['redirect'];
    }
}
    // On vérifie si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Récupération et nettoyage des données saisies : trim() retire les espaces accidentels en début/fin de chaîne
        $email = trim($_POST['email'] ?? '');     
        $password = $_POST['password'] ?? ''; // Si le champ password n'est pas défini, on lui donne une valeur vide pour éviter les erreurs

        // Connexion à la base de données
        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM vg_utilisateur WHERE email = :email");
        $stmt->execute(['email' => $email]);

        // Récupération de la première ligne de résultat 
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Vérif : l'utilisateur existe-t-il ET le mot de passe est-il correct ?

        if ($user && password_verify($password, $user['password'])) {
            

if (isset($user['est_actif']) && (int)$user['est_actif'] === 0) {                // Le compte est banni ou suspendu
                $error = "Votre compte a été suspendu par un administrateur. Veuillez contacter le support.";
                require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/ban.errormessage.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
                exit();
                 // On arrête le script pour bloquer la connexion
            }

            // SI LE COMPTE EST ACTIF, CA REPREND LE FLUX NORMAL 


            // Succès : On remplit la session avec les données utiles pour l'utilisateur connecté
           $_SESSION['user_id'] = $user['utilisateur_id'];
                    $_SESSION['email']   = $user['email'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['nom']     = $user['nom'];
                    $_SESSION['prenom']  = $user['prenom'];
                    $_SESSION['telephone'] = $user['telephone'];
                    $_SESSION['adresse_postale'] = $user['adresse_postale'];
                    $_SESSION['ville']   = $user['ville'];
                    $_SESSION['pays']    = $user['pays'];



            $_SESSION['show_welcome'] = true;
            // Marqueur pour afficher la pop-up de bienvenue sur le dashboard après la redirection 
            // 1. D'abord, on gère la redirection prioritaire (commande en attente)
if (isset($_SESSION['redirect_after_login'])) {
    $url = $_SESSION['redirect_after_login'];
    unset($_SESSION['redirect_after_login']);
    header('Location: ' . $url);
    exit();
}

// 2. Sinon, on gère la redirection par rôle
$_SESSION['show_welcome'] = true;
$destination = match ((int)($_SESSION['role_id'] ?? 0)) {
    1 => 'index.php?page=dashboard-admin',
    2 => 'index.php?page=dashboard-employee',
    3 => 'index.php?page=dashboard-user',
    default => 'index.php?page=home',
};

// Nettoyage avant redirection
if (ob_get_length()) ob_clean();

header('Location: ' . $destination);
exit();
        } else {
            // Échec : message d'erreur générique pour éviter de donner des indices aux attaquants
            $error = "Identifiants invalides. Veuillez vérifier votre email et votre mot de passe.";
        }
    }

    // PRÉPARATION DE LA VUE

    $title = "Se connecter - EcoRide";

    // Chargement des polices et styles spécifiques à la page de connexion
    $specifics_fonts = "https://fonts.googleapis.com/css?family=Lexend&display=swap";

    $specific_styles = [
        "../public/assets/css/loginsignin.css",
    ];

    $specific_scripts = [];

    // Inclusion des fichiers de template pour l'affichage
   require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/Auth/login.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
}
}