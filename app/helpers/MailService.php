<?php

namespace app\Helpers;

class MailService
{
    public static function sendWelcomeEmail(string $toEmail): bool
    {
        ini_set('SMTP', '127.0.0.1');
        ini_set('smtp_port', '1025');

        $subject = "Bienvenue chez Vite Gourmand !";
        $message = "Bonjour, bienvenue sur notre site !";
        $headers = 'From: noreply@vite-gourmand.fr' . "\r\n" .
            'Reply-To: contact@vite-gourmand.fr' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        // 2. On envoie
        return mail($toEmail, $subject, $message, $headers);
    }

    public static function sendResetEmail(string $toEmail, string $resetLink): bool
    {
        ini_set('SMTP', '127.0.0.1');
        ini_set('smtp_port', '1025');

        $subject = "Réinitialisation de votre mot de passe";
        $message = "Bonjour, vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien suivant pour procéder à la réinitialisation : $resetLink";
        $headers = 'From: noreply@vite-gourmand.fr' . "\r\n" .
            'Reply-To: contact@vite-gourmand.fr' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        // 2. On envoie
        return mail($toEmail, $subject, $message, $headers);
    }

    public static function sendOrderConfirmationEmail(string $toEmail, array $orderDetails): bool
    {
        ini_set('SMTP', '127.0.0.1');
        ini_set('smtp_port', '1025');

        $subject = "Confirmation de votre commande - Vite Gourmand";

        // Construction d'un message lisible
        $message = "Bonjour " . ($orderDetails['prenom'] ?? 'client') . ",\n\n";
        $message .= "Nous avons le plaisir de vous confirmer la prise en compte de votre commande.\n\n";
        $message .= "Détails de votre commande :\n";
        $message .= "- Menu : " . ($orderDetails['menu'] ?? 'Non défini') . "\n";
        $message .= "- Quantité : " . ($orderDetails['quantite'] ?? 0) . " personne(s)\n";
        $message .= "- Date : " . ($orderDetails['date_prestation'] ?? 'Non définie') . "\n";
        $message .= "- Heure : " . ($orderDetails['heure_livraison'] ?? 'Non définie') . "\n";
        $message .= "- Lieu : " . ($orderDetails['lieu'] ?? 'Non défini') . "\n";
        $message .= "- Prix total : " . number_format($orderDetails['prix_total'] ?? 0, 2) . " €\n\n";
        $message .= "Merci de votre confiance,\nL'équipe Vite Gourmand.";

        // Ajout du Content-Type pour supporter l'UTF-8 (accents)
        $headers = "From: noreply@vite-gourmand.fr\r\n" .
            "Reply-To: contact@vite-gourmand.fr\r\n" .
            "Content-Type: text/plain; charset=utf-8\r\n" .
            "X-Mailer: PHP/" . phpversion();

        return mail($toEmail, $subject, $message, $headers);
    }
    public static function sendOrderCancellationEmail(string $toEmail, array $orderDetails): bool
    {
        ini_set('SMTP', '127.0.0.1');
        ini_set('smtp_port', '1025');

        $menuName = $orderDetails['menu']['titre'] ?? 'votre menu';
        $orderId = $orderDetails['commande_id'] ?? 'N/A';

        $subject = "Annulation de votre commande n°" . $orderId . "";

        $message = "Bonjour,\n\n";
        $message .= "Nous vous confirmons l'annulation de votre commande n°" . $orderId . " concernant le menu : " . $menuName . ".\n\n";
        $message .= "Si vous avez des questions, n'hésitez pas à nous contacter.";

        $headers = 'From: noreply@vite-gourmand.fr' . "\r\n" .
            'Reply-To: contact@vite-gourmand.fr' . "\r\n" .
            'Content-Type: text/plain; charset=utf-8' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        return mail($toEmail, $subject, $message, $headers);
    }

    public static function sendOrderUpdateEmail(string $toEmail, array $orderDetails): bool
    {
        ini_set('SMTP', '127.0.0.1');
        ini_set('smtp_port', '1025');

        $subject = "Mise à jour de votre commande n°" . ($orderDetails['commande_id'] ?? 'N/A') . "";

        $message = "Bonjour,\n\n";
        $message .= "Nous vous confirmons la mise à jour de votre commande n°" . ($orderDetails['commande_id'] ?? 'N/A') . ".\n\n";
        $message .= "Détails de votre commande :\n";
        $message .= "- Menu : " . ($orderDetails['menu']['titre'] ?? 'Non défini') . "\n";
        $message .= "- Quantité : " . ($orderDetails['quantite'] ?? 0) . " personne(s)\n";
        $message .= "- Date : " . ($orderDetails['date_prestation'] ?? 'Non définie') . "\n";
        $message .= "- Heure : " . ($orderDetails['heure_livraison'] ?? 'Non définie') . "\n";
        $message .= "- Lieu : " . ($orderDetails['lieu'] ?? 'Non défini') . "\n";
        $message .= "- Prix total : " . number_format($orderDetails['total_final'] ?? 0, 2) . " €\n\n";
        $message .= "Merci pour votre confiance,\nL'équipe Vite Gourmand.";

        $headers = 'From: noreply@vite-gourmand.fr' . "\r\n" .
            'Reply-To: contact@vite-gourmand.fr' . "\r\n" .
            'Content-Type: text/plain; charset=utf-8' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        return mail($toEmail, $subject, $message, $headers);
    }
    public static function sendMaterialReturnReminderEmail(string $toEmail, array $orderDetails, string $commentaire): bool
    {
        ini_set('SMTP', '127.0.0.1');
        ini_set('smtp_port', '1025');

        $orderId = $orderDetails['commande_id'] ?? 'N/A';
        $prenom = $orderDetails['prenom'] ?? $orderDetails['client_nom'] ?? 'Client';

        $subject = "Rappel important : Restitution du matériel - Commande n°" . $orderId;

        $message = "Bonjour " . $prenom . ",\n\n";
        $message .= "Nous vous rappelons que vous avez du matériel en prêt dans le cadre de votre commande n°" . $orderId . ".\n\n";
        $message .= "Rappel : Si le matériel n'est pas restitué sous 10 jours ouvrés, des frais de 600€ s'appliquent conformément à nos CGV.\n\n";

        if (!empty($commentaire)) {
            $message .= "Message de notre équipe :\n" . $commentaire . "\n\n";
        }

        $message .= "Merci de bien vouloir procéder à sa restitution rapide.\n\n";
        $message .= "Cordialement,\nL'équipe Vite Gourmand.";

        $headers = 'From: noreply@vite-gourmand.fr' . "\r\n" .
            'Reply-To: contact@vite-gourmand.fr' . "\r\n" .
            'Content-Type: text/plain; charset=utf-8' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        return mail($toEmail, $subject, $message, $headers);
    }
}
