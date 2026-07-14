<?php
namespace App\Controllers\AuthController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once __DIR__ . '/Auth.php';

use App\Controllers\AuthController\Auth; // On importe la classe Auth pour pouvoir utiliser la méthode setUserSession() après une connexion réussie
class SigninController
{
    // fonction qu'on appelle pour afficher la page depuis l'index.php puis s'inscrire
    public static function SignIn(\PDO $db)
   
    {
        $errors = [];

        if (isset($_GET['redirect'])) {
        $_SESSION['redirect_after_login'] = $_GET['redirect'];
    }
        $pdo = $db;
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // Nettoyage complet des entrées
            $nom = strip_tags(trim($_POST['nom'] ?? ''));
            $prenom = strip_tags(trim($_POST['prenom'] ?? ''));
            $gsm = strip_tags(trim($_POST['gsm'] ?? ''));
            $adresse_postale = strip_tags(trim($_POST['adresse_postale'] ?? ''));
            $ville = strip_tags(trim($_POST['ville'] ?? ''));
            $pays = strip_tags(trim($_POST['pays'] ?? ''));

            // 1, 2, 3 : Tes vérifications de mot de passe sont bonnes.
            if ($password !== $password_confirm) {
                $errors['password_confirm'] = "Les mots de passe ne correspondent pas.";
            } elseif (strlen($password) < 10) {
                $errors['password'] = "Le mot de passe doit faire au moins 10 caractères.";
            } else {
                $passwordRegex = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.]).{10,}$/";
                if (!preg_match($passwordRegex, $password)) {
                    $errors['password'] = "Le mot de passe doit contenir au moins 10 caractères, une majuscule, un minuscule, un chiffre et un caractère spécial.";
                }
            }

            // 4. SI TOUT EST BON : Insérer en DB
            
            if (empty($errors)) {


                $stmtCheck = $db->prepare("SELECT email FROM vg_utilisateur WHERE email = :email");
                $stmtCheck->execute(['email' => $email]);

                if ($stmtCheck->fetch()) {
                    $errors['email'] = "Cet email est déjà utilisé par un autre compte.";
                } else {


                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    try {
                        $sql = "INSERT INTO vg_utilisateur (nom, prenom, telephone, email, password, role_id, adresse_postale, ville, pays) 
                        VALUES (:nom, :prenom, :telephone, :email, :password, 3, :adresse_postale, :ville, :pays)";

                        $stmt = $db->prepare($sql);
                        $stmt->execute([
                            'nom'             => $nom,
                            'prenom'          => $prenom,
                            'telephone'       => $gsm,
                            'email'           => $email,
                            'password'        => $hashedPassword,
                            'adresse_postale' => $adresse_postale,
                            'ville'           => $ville,
                            'pays'            => $pays
                        ]);

                        $userId = $db->lastInsertId();

                        // Récupération des données pour la session
                        $stmtSelect = $db->prepare("SELECT * FROM vg_utilisateur WHERE utilisateur_id = :id");
                        $stmtSelect->execute(['id' => $userId]);
                        $user = $stmtSelect->fetch(\PDO::FETCH_ASSOC);

                        if ($user) {
                            Auth::setUserSession($user);
                            $_SESSION['show_welcome'] = true;
                            \App\Helpers\MailService::sendWelcomeEmail($email);
                        }
if (ob_get_length()) ob_clean();

if (isset($_SESSION['redirect_after_login'])) {
    $url = $_SESSION['redirect_after_login'];
    // Sécurité : Optionnel mais conseillé
    if (strpos($url, 'index.php?page=order-menu') === 0) {
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $url);
        exit();
    }
}

// Redirection classique si pas de redirection spécifique
header('Location: index.php?page=dashboard-user');
exit();
                    } catch (\PDOException $e) {
                        $errors['general'] = ($e->getCode() == 23000) ? "Ces identifiants sont déjà utilisés." : "Une erreur est survenue.";
                    }
                }
            }
        }

        $title = "S'inscrire - V&G";

        $specifics_fonts = "https://fonts.googleapis.com/css?family=Lexend&display=swap";

        // fichiers CSS spécifiques à cette page
        $specific_styles = [
            "../public/assets/css/loginsignin.css",
        ];

        // Pareil pour le JS
        $specific_scripts = [];
        $errors = $errors ?? [];
        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/Auth/signin.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
