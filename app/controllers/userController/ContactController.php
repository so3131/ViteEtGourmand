<?php
namespace App\Controllers\UserController;
require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once ROOT_PATH . '/app/helpers/FormHelper.php';


class ContactController 
{
public static function contactUs(\PDO $db)
{
    $errors = [];


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 1. Récupération et nettoyage
        $nom = strip_tags(trim($_POST['nom'] ?? ''));
        $prenom = strip_tags(trim($_POST['prenom'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $sujet = strip_tags(trim($_POST['sujet'] ?? ''));
        $message = strip_tags(trim($_POST['message'] ?? ''));
        $nomComplet = $prenom . ' ' . $nom;

        // 2. Validation
        if (empty($nom) || empty($prenom) || !$email || empty($sujet) || empty($message)) {
            $errors[] = "Veuillez remplir correctement tous les champs obligatoires.";
        } else {
            // 3. Préparation du mail
            $to = "sofiene31@hotmail.com";
            $subject = "[Vite & Gourmand] Nouveau contact : " . $sujet;
            $emailContent = "Vous avez reçu une nouvelle demande de contact.\n\n" .
                "De : $nomComplet ($email)\n" .
                "Sujet : $sujet\n\n" .
                "Message :\n$message";

            $headers = "From: contact@vite-et-gourmand.fr\r\n" .
                "Reply-To: $email\r\n" .
                "X-Mailer: PHP/" . phpversion();

            // 4. Envoi avec correction de syntaxe
            if (mail($to, $subject, $emailContent, $headers)) {
                header('Location: index.php?page=contact-success');
                exit;
            } else {
                // Mail de secours
                $secoursSubject = "[URGENT] Échec envoi mail contact - Vite & Gourmand";
                $secoursContent = "Une tentative de contact a échoué via la fonction mail principale.\n" . $emailContent;
                @mail($to, $secoursSubject, $secoursContent, $headers);

                header('Location: index.php?page=contact-success');
                exit;
            }
        }
    }

    // Affichage de la page
    $title = "Nous contacter - Vite & Gourmand";
    $specifics_fonts = "https://fonts.googleapis.com/css?family=Lexend:400,500,600&display=swap";

    $specific_styles = ["assets/css/styleContact.css", "assets/css/MQContact.css"];
    $specific_scripts = ["assets/javascript/formulaire.js", "assets/javascript/evenements.js"];

    require_once ROOT_PATH . '/app/views/layout/header.php';
    require_once ROOT_PATH . '/app/views/user/contact.view.php';
    require_once ROOT_PATH . '/app/views/layout/footer.php';
}
}