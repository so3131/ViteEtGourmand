<?php
namespace app\Helpers;

class MailService {
    public static function sendWelcomeEmail(string $toEmail): bool {
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

    public static function sendResetEmail(string $toEmail, string $resetLink): bool {
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
}
