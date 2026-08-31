<?php
namespace App\Controllers\UserController;

use App\Helpers\MailService;

require_once dirname(__DIR__, 2) . '/config/constants.php';
require_once ROOT_PATH . '/app/helpers/FormHelper.php';

class ContactController 
{
    //function pour afficher la page de contact et gérer l'envoi du formulaire
    public static function contactUs(\PDO $db)
    {
        $errors = $errors ?? [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Récupération et nettoyage
            $nom = strip_tags(trim($_POST['nom'] ?? ''));
            $prenom = strip_tags(trim($_POST['prenom'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
            $sujet = strip_tags(trim($_POST['sujet'] ?? ''));
            $message = strip_tags(trim($_POST['message'] ?? ''));

            // 2. Validation
            if (empty($nom) || empty($prenom) || !$email || empty($sujet) || empty($message)) {
                $errors[] = "Veuillez remplir correctement tous les champs obligatoires.";
            } else {
                // 3. Construction des données pour l'envoi via Brevo (MailService)
                $contactDetails = [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'sujet' => $sujet,
                    'message' => $message
                ];

                // 4. Envoi du mail via ton service Brevo
                
                $mailSent = MailService::sendContactEmail($contactDetails);

                if ($mailSent) {
                    $success = true;
                    // Optionnel : vider le $_POST pour réinitialiser le formulaire
                    $_POST = [];
                } else {
                    $errors[] = "Une erreur est survenue lors de l'envoi du message. Veuillez réessayer.";
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