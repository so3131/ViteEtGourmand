<?php

namespace App\Helpers;
// class SecurityManager pour gérer la sécurité et l'accès aux pages en fonction des rôles des utilisateurs
class SecurityManager
{
    //function pour vérifier l'accès à une page en fonction du rôle de l'utilisateur
    public static function checkAccess(
        string $page,
        ?int $role_id,
        array $pagesPubliques,
        array $pagesQuiExistent,
        array $pagesAdmin,
        array $pagesEmployee,
        array $pagesUser,
        array $pagesStaff
    ): string {

        // Étape A : La page n'existe pas -> 404 direct
        if (!in_array($page, $pagesQuiExistent)) {
            return '404';
        }

        // Étape B : La page est privée et l'utilisateur n'est pas connecté -> Direction Login
        if (!in_array($page, $pagesPubliques) && $role_id === null) {
            header('Location: index.php?page=login');
            exit();
        }

        // Étape C : L'utilisateur EST connecté, contrôle des rôles
        if ($role_id !== null) {
            $role = (int)$role_id;

            if (in_array($page, $pagesAdmin) && $role !== \ROLE_ADMIN) {
                return '404';
            }

            if (in_array($page, $pagesEmployee) && $role !== \ROLE_EMPLOYE && $role !== \ROLE_ADMIN) {
                return '404';
            }

            if (in_array($page, $pagesStaff) && $role !== \ROLE_ADMIN && $role !== \ROLE_EMPLOYE) {
                return '404';
            }

            if (in_array($page, $pagesUser)) {
                if ($role !== \ROLE_USER && $role !== \ROLE_ADMIN && $role !== \ROLE_EMPLOYE) {
                    return '404';
                }
            }
        }

        return $page;
    }
    /**
     * function pour valider les requêtes POST avec un jeton CSRF
     * @param string $redirectOnError L'URL de redirection en cas d'échec
     */
    public static function validatePost(string $redirectOnError = '?page=home'): int
    {
        //  Vérification de la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirectOnError . '&error=invalid_method');
            exit();
        }

        // Vérification du jeton CSRF
        if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
            $_SESSION['error'] = "Session expirée ou requête invalide. Veuillez recharger la page.";
            header('Location: ' . $redirectOnError);
            exit();
        }

        // 3. Retourne l'ID sécurisé s'il est présent
        return isset($_POST['id']) ? (int)$_POST['id'] : 0;
    }
  //function pour valider les requêtes JSON avec un jeton CSRF
    /**
     * Valide une requête JSON en vérifiant le jeton CSRF.
     *
     * @param array $data Les données JSON à valider.
     * @return void
     */
public static function validateJson(array $data): void
{
    $csrfToken = $data['csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (
        empty($sessionToken) ||
        empty($csrfToken) ||
        !hash_equals($sessionToken, $csrfToken)
    ) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'message' => 'Requête invalide ou session expirée.'
        ]);

        exit();
    }
}
}
