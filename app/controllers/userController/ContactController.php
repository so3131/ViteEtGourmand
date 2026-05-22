<?php
// ContactController

require_once dirname(__DIR__, 2) . '/config/Constants.php';

class ContactController
{
    public static function contactUs(PDO $db)
    {
        $title = "Contact - Vite & Gourmand";
        $error = null;
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = strip_tags(trim($_POST['name'] ?? ''));
            $email = trim($_POST['email'] ?? '');
            $message = strip_tags(trim($_POST['message'] ?? ''));

            if (empty($name) || empty($email) || empty($message)) {
                $error = "Tous les champs sont requis.";
            } else {
                // Envoyer l'email ou stocker le message
                $success = true;
            }
        }

        require_once ROOT_PATH . '/app/views/layout/header.php';
        require_once ROOT_PATH . '/app/views/user/contact.view.php';
        require_once ROOT_PATH . '/app/views/layout/footer.php';
    }
}
