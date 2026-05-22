<?php
require_once dirname(__DIR__, 2) . '/config/Constants.php';
require_once __DIR__ . '/Auth.php';

use App\Controllers\AuthController\Auth;

class SigninController
{
    public static function SignIn(PDO $db)
    {
        $title = "Inscription - Vite & Gourmand";
        $specific_styles = ["assets/css/auth.css"];
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $nom = strip_tags(trim($_POST['nom'] ?? ''));
            $prenom = strip_tags(trim($_POST['prenom'] ?? ''));

            if ($password !== $password_confirm) {
                $error = "Les mots de passe ne correspondent pas.";
            } elseif (!Auth::validatePassword($password)) {
                $error = "Le mot de passe doit faire au moins 8 caractères avec majuscule, minuscule, chiffre et caractère spécial.";
            } elseif (Auth::emailExists($db, $email)) {
                $error = "Cet email est déjà utilisé.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                try {
                    $sql = "INSERT INTO vg_utilisateur (nom, prenom, email, password, role_id) 
                            VALUES (:nom, :prenom, :email, :password, :role_id)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([
                        'nom' => $nom,
                        'prenom' => $prenom,
                        'email' => $email,
                        'password' => $hashedPassword,
                        'role_id' => ROLE_USER
                    ]);

                    $userId = $db->lastInsertId();
                    $stmt = $db->prepare("SELECT * FROM vg_utilisateur WHERE utilisateur_id = :id");
                    $stmt->execute(['id' => $userId]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    Auth::setUserSession($user);
                    header('Location: index.php?page=home');
                    exit();
                } catch (PDOException $e) {
                    $error = "Erreur lors de l'inscription.";
                }
            }
        }

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/Auth/signin.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
