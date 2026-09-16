<?php

namespace App\Helpers;

use Brevo\Brevo;
use Brevo\TransactionalEmails\Requests\SendTransacEmailRequest;
use GuzzleHttp\Client;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestSender;
use Brevo\TransactionalEmails\Types\SendTransacEmailRequestToItem;

class MailService
{
    /**
     * Factoriser l'envoi via Brevo
     */
    public static function sendEmail(string $toEmail, string $toName, string $subject, string $textContent, ?string $htmlContent = null): bool
    {
        $apiKey = getenv('BREVO_API_KEY') ?: '';

        if (empty($apiKey)) {
            error_log('Erreur Brevo : Clé API manquante.');
            return false;
        }

        try {
            $brevo = new Brevo($apiKey, [
                'client' => new Client([
                    'connect_timeout' => 5,
                    'timeout'         => 10,
                ]),
            ]);
            $sender = new SendTransacEmailRequestSender([
                'name' => 'Vite Gourmand',
                'email' => getenv('MAIL_FROM_EMAIL') ?: 'sofiene31@hotmail.com'


            ]);

            $toItem = new SendTransacEmailRequestToItem([
                'email' => $toEmail,
                'name' => $toName ?: 'Client'
            ]);

            $emailData = [
                'subject' => $subject,
                'sender' => $sender,
                'to' => [$toItem]
            ];

            if ($htmlContent) {
                $emailData['htmlContent'] = $htmlContent;
            } else {
                $emailData['textContent'] = $textContent;
            }

            $brevo->transactionalEmails->sendTransacEmail(
                new SendTransacEmailRequest($emailData)
            );

            return true;
        } catch (\Exception $e) {
            error_log('Erreur Brevo API : ' . $e->getMessage());
            return false;
        }
    }
    //function pour envoyer un email de bienvenue à un nouvel utilisateur
    public static function sendWelcomeEmail(string $toEmail): bool
    {
        $subject = "Bienvenue chez Vite Gourmand !";
        $textContent = "Bonjour,\n\nBienvenue sur notre site !\n\nNous sommes ravis de vous compter parmi nous.\n\nCordialement,\nL'équipe Vite Gourmand.";

        return self::sendEmail($toEmail, 'Nouveau client', $subject, $textContent);
    }
    // Function pour envoyer un mail de confirmation de création de compte à un employé
    public static function sendAccountCreationEmail(string $toEmail): bool
    {
        $subject = "Création de votre compte Vite & Gourmand";
        $textContent = "Bonjour,\n\nUn compte employé vient d'être créé pour vous sur Vite & Gourmand.\nVotre identifiant est : " . $toEmail . "\n\nVeuillez vous rapprocher de votre administrateur pour obtenir votre mot de passe.\n\nCordialement,\nL'équipe Vite & Gourmand.";
        return self::sendEmail($toEmail, 'Employé', $subject, $textContent);
    }
    //function pour envoyer un email de contact à l'administrateur du site
    public static function sendContactEmail(array $data): bool
    {
        $nomComplet = htmlspecialchars($data['prenom'] . ' ' . $data['nom']);
        $clientEmail = $data['email'];
        $sujet = '[Vite & Gourmand] Contact : ' . $data['sujet'];
        $messageContenu = nl2br(htmlspecialchars($data['message']));

        $htmlContent = "
            <html>
            <body style='font-family: Arial, sans-serif; color: #333;'>
                <h2 style='color: #fd7e14;'>Nouveau message de contact - Vite & Gourmand</h2>
                <p><strong>De :</strong> {$nomComplet} (<a href='mailto:{$clientEmail}'>{$clientEmail}</a>)</p>
                <p><strong>Sujet / Motif :</strong> " . htmlspecialchars($data['sujet']) . "</p>
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                <p><strong>Message :</strong></p>
                <p style='background-color: #f8f9fa; padding: 15px; border-radius: 5px;'>{$messageContenu}</p>
            </body>
            </html>
        ";

        return self::sendEmail('sofiene31@hotmail.com', 'Sofiene', $sujet, '', $htmlContent);
    }
    //function pour envoyer un email de réinitialisation de mot de passe à un utilisateur
    public static function sendResetEmail(string $toEmail, string $resetLink, string $prenom = 'Client'): bool
    {
        $subject = "Réinitialisation de votre mot de passe";
        $textContent = "Bonjour " . $prenom . ",\n\nVous avez demandé à réinitialiser votre mot de passe.\n\nCliquez sur le lien suivant pour procéder à la réinitialisation :\n" . $resetLink . "\n\nSi vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail.\n\nCordialement,\nL'équipe Vite Gourmand.";

        return self::sendEmail($toEmail, $prenom, $subject, $textContent);
    }
    //function pour envoyer un email de confirmation de réinitialisation de mot de passe à un utilisateur
    public static function sendResetConfirmationEmail(string $toEmail, string $prenom = 'client'): bool
    {
        $subject = "Sécurité : Votre mot de passe a été modifié - Vite Gourmand";
        $textContent = "Bonjour " . $prenom . ",\n\nNous vous confirmons que le mot de passe de votre compte Vite & Gourmand a été modifié avec succès.\n\nSi vous n'êtes pas à l'origine de cette modification, veuillez nous contacter immédiatement.\n\nL'équipe Vite Gourmand.";

        return self::sendEmail($toEmail, $prenom, $subject, $textContent);
    }
    //function pour envoyer une confirmation de commande à un client
    public static function sendOrderConfirmationEmail(string $toEmail, array $orderDetails, string $prenom = 'client'): bool
    {
        $prenom = $orderDetails['prenom'] ?? ($prenom !== 'client' ? $prenom : 'client');
        $subject = "Confirmation de votre commande - Vite Gourmand";

        $textContent = "Bonjour " . $prenom . ",\n\n";
        $textContent .= "Nous avons le plaisir de vous confirmer la prise en compte de votre commande.\n\n";
        $textContent .= "Détails de votre commande :\n";
        $textContent .= "- Menu : " . ($orderDetails['menu'] ?? 'Non défini') . "\n";
        $textContent .= "- Quantité : " . ($orderDetails['quantite'] ?? 0) . " personne(s)\n";
        $textContent .= "- Date : " . ($orderDetails['date_prestation'] ?? 'Non définie') . "\n";
        $textContent .= "- Heure : " . ($orderDetails['heure_livraison'] ?? 'Non définie') . "\n";
        $textContent .= "- Lieu : " . ($orderDetails['lieu'] ?? 'Non défini') . "\n";
        $textContent .= "- Prix total : " . number_format($orderDetails['prix_total'] ?? 0, 2) . " €\n\n";
        $textContent .= "Merci de votre confiance,\nL'équipe Vite Gourmand.";

        return self::sendEmail($toEmail, $prenom, $subject, $textContent);
    }
    //function pour envoyer un email de mise à jour de commande à un client
    public static function sendOrderUpdateEmail(string $toEmail, array $orderDetails): bool
    {
        $prenom = $orderDetails['prenom'] ?? 'client';
        $subject = "Mise à jour de votre commande - Vite Gourmand";

        $textContent = "Bonjour " . $prenom . ",\n\n";
        $textContent .= "Votre commande a été mise à jour avec succès.\n\n";
        $textContent .= "Détails mis à jour de votre commande :\n";
        $textContent .= "- Menu : " . ($orderDetails['menu'] ?? 'Non défini') . "\n";
        $textContent .= "- Quantité : " . ($orderDetails['quantite'] ?? 0) . " personne(s)\n";
        $textContent .= "- Date : " . ($orderDetails['date_prestation'] ?? 'Non définie') . "\n";
        $textContent .= "- Heure : " . ($orderDetails['heure_livraison'] ?? 'Non définie') . "\n";
        $textContent .= "- Lieu : " . ($orderDetails['lieu'] ?? 'Non défini') . "\n";
        $textContent .= "- Prix total : " . number_format($orderDetails['prix_total'] ?? 0, 2) . " €\n\n";
        $textContent .= "Merci de votre confiance,\nL'équipe Vite Gourmand.";

        return self::sendEmail($toEmail, $prenom, $subject, $textContent);
    }
    //function pour envoyer un email de mise à jour de statut de commande à un client
    public static function sendOrderStatusUpdateEmail(string $toEmail, array $orderDetails, string $statut, string $prenom = 'client'): bool
    {
        $prenom = $orderDetails['prenom'] ?? ($prenom !== 'client' ? $prenom : 'client');
        $subject = "Mise à jour de votre commande n°" . ($orderDetails['commande_id'] ?? 'N/A') . " - Vite Gourmand";

        $textContent = "Bonjour " . $prenom . ",\n\n";
        $textContent .= "Nous vous informons que le statut de votre commande n°" . ($orderDetails['commande_id'] ?? 'N/A') . " a été mis à jour.\n\n";
        $textContent .= "Nouveau statut : " . ucfirst(str_replace('_', ' ', $statut)) . "\n\n";
        $textContent .= "Merci de votre confiance,\nL'équipe Vite Gourmand.";

        return self::sendEmail($toEmail, $prenom, $subject, $textContent);
    }

    //function pour envoyer un email d'annulation de commande à un client
    public static function sendOrderCancellationEmail(string $toEmail, array $orderDetails, string $commentaire = ''): bool
    {
        $menuName = $orderDetails['menu']['titre'] ?? 'votre menu';
        $orderId = $orderDetails['commande_id'] ?? 'N/A';
        $prenom = $orderDetails['prenom'] ?? $orderDetails['client_nom'] ?? 'Client';

        $subject = "Annulation de votre commande n°" . $orderId;

        $textContent = "Bonjour " . $prenom . ",\n\n";
        $textContent .= "Nous vous confirmons l'annulation de votre commande n°" . $orderId . " concernant le menu : " . $menuName . ".\n\n";

        if (!empty($commentaire)) {
            $textContent .= "Message de notre équipe :\n" . $commentaire . "\n\n";
        }

        $textContent .= "Si vous avez des questions, n'hésitez pas à nous contacter.\n\nCordialement,\nL'équipe Vite Gourmand.";

        return self::sendEmail($toEmail, $prenom, $subject, $textContent);
    }
    //function pour envoyer un email de rappel de restitution de matériel à un client
    public static function sendMaterialReturnReminderEmail(string $toEmail, array $orderDetails, string $commentaire): bool
    {
        $orderId = $orderDetails['commande_id'] ?? 'N/A';
        $prenom = $orderDetails['prenom'] ?? $orderDetails['client_nom'] ?? 'Client';

        $subject = "Rappel important : Restitution du matériel - Commande n°" . $orderId;

        $textContent = "Bonjour " . $prenom . ",\n\n" .
            "Nous vous rappelons que vous avez du matériel en prêt dans le cadre de votre commande n°" . $orderId . ".\n\n" .
            "Rappel : Si le matériel n'est pas restitué sous 10 jours ouvrés, des frais de 600€ s'appliquent conformément à nos CGV.\n\n" .
            "Pour organiser la restitution, merci de bien vouloir répondre à cet e-mail ou de prendre directement contact avec notre équipe.\n\n";

        if (!empty($commentaire)) {
            $textContent .= "Message de notre équipe :\n" . $commentaire . "\n\n";
        }

        $textContent .= "Cordialement,\nL'équipe Vite Gourmand.";

        return self::sendEmail($toEmail, $prenom, $subject, $textContent);
    }
    //function pour envoyer un email de demande d'avis à un client après la livraison de sa commande
    public static function sendReviewEmail(string $toEmail, array $orderDetails, string $prenom = 'client'): bool
    {
        $orderId = $orderDetails['commande_id'] ?? 'N/A';
        $prenom = $orderDetails['prenom'] ?? $prenom;

        $subject = "Votre commande n°" . $orderId . " est terminée - Donnez votre avis ! - Vite Gourmand";

        $textContent = "Bonjour " . $prenom . ",\n\n";
        $textContent .= "Nous vous informons que votre commande n°" . $orderId . " est désormais terminée.\n\n";
        $textContent .= "Vous pouvez dès à présent vous connecter à votre compte sur Vite Gourmand pour consulter l'historique de votre commande et nous laisser votre avis (noté de 1 à 5 avec un commentaire).\n\n";
        $textContent .= "Merci de votre confiance et à bientôt !\n\nL'équipe Vite Gourmand.";

        return self::sendEmail($toEmail, $prenom, $subject, $textContent);
    }
}
