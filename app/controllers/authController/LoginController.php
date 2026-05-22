<?php
require_once dirname(__DIR__, 2) . '/config/Constants.php';
require_once __DIR__ . '/Auth.php';

use App\Controllers\AuthController\Auth;

class LoginController
{
    public static function LogIn(PDO $db)
    {
        $title = "Connexion - Vite & Gourmand";
        $specific_styles = ["assets/css/auth.css"];
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = "Email et mot de passe requis.";
            } else {
                $stmt = $db->prepare("SELECT * FROM vg_utilisateur WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    Auth::setUserSession($user);
                    header('Location: index.php?page=home');
                    exit();
                } else {
                    $error = "Email ou mot de passe incorrect.";
                }
            }
        }

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/Auth/login.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
