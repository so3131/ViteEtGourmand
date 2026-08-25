# Mémo - Correction des routes

Ce mémo sert à corriger les boutons, formulaires et actions qui ne sont pas encore correctement reliés dans `public/index.php`.

Travaille dans l'ordre. Après chaque étape, teste l'action dans le navigateur avant de passer à la suivante.

## Étape 1 - Corriger le mot de passe oublié

Fichiers concernés :

- `public/index.php`
- `app/views/Auth/login.view.php`
- `app/views/Auth/log.reset.view.php`
- `app/Controllers/AuthController/ForgotPasswordController.php`

À faire :

- [x ] Remplacer `page=password-forgotten` par `page=forgot-password` dans le lien de connexion.
- [x ] Remplacer `page=password-forgotten-send` par `page=forgot-password` dans le formulaire.
- [x ] Vérifier que le formulaire utilise bien `method="POST"`.
- [ ] Vérifier que le contrôleur reçoit bien l'adresse email.
- [ ] Vérifier que l'adresse inconnue affiche un message générique.

Test : cliquer sur « Mot de passe oublié », envoyer une adresse connue puis une adresse inconnue.

## Étape 2 - Corriger le profil utilisateur

Fichiers concernés :

- `app/views/Auth/update.profile.view.php`
- `app/Controllers/AuthController/updateProfilController.php`

À faire :

- [ x] Remplacer `page=updateProfil` par `page=update-profil`.
- [x ] Remplacer les redirections vers `page=dashboard` par `page=dashboard-user`.
- [ x] Vérifier que la modification du profil est enregistrée.
- [x ] Vérifier que l'utilisateur revient bien sur son tableau de bord.

Test : modifier le téléphone ou l'adresse d'un compte utilisateur.

## Étape 3 - Ajouter les routes de gestion des menus

Fichiers concernés :

- `public/index.php`
- `app/Controllers/StaffCommon/MenuManagementController.php`
- `app/views/StaffCommon/menu.management.view.php`

Les méthodes existent déjà : `addMenu()` et `deleteMenu()`.

À faire :

- [x] Ajouter la route `add-menu-process`.
- [x] Ajouter la route `delete-menu`.
- [x] Ajouter le paramètre `menu_id`.
- [x] Vérifier les droits administrateur/employé.
- [x] Tester l’ajout et la suppression d’un menu.
- [x ] Remplacer les liens de suppression en `GET` par un formulaire `POST`.

Test : créer un menu de test, vérifier qu'il apparaît, puis le supprimer.

## Étape 4 - Corriger les routes employé

Fichiers concernés :

- `app/views/employee/dashboard.employee.view.php`
- `public/index.php`
- `app/Config/pages.php`

À faire :


- [ x] Remplacer `page=moderation-employee` par `page=review-management`.
- [x ] Vérifier que les routes `conflict` et `moderation` sont autorisées pour l'employé.
- [ ] Vérifier que les pages s'ouvrent sans erreur 404.

Test : se connecter avec un compte employé et ouvrir les deux pages.

## Étape 5 - Corriger la modification de commande employé

Fichiers concernés :

- `public/assets/javascript/EditOrderCommon.js`
- `public/index.php`
- `app/Config/pages.php`
- `app/Controllers/StaffCommon/EditOrderController.php`

À faire :

- [ x] Dans `EditOrderCommon.js`, remplacer `page=recalculer-prix` par `page=recalculer-prix-common`.
- [x ] Ajouter `recalculer-prix-common` dans les routes autorisées pour l'employé.
- [ x] Ajouter `cancel-edit-order-common` dans les routes autorisées pour l'employé.
- [ x] Vérifier que `commande_id` est transmis.
- [ x] Vérifier que l'employé ne peut modifier que les commandes autorisées.

Test : ouvrir une commande employé, modifier le nombre de personnes et vérifier le nouveau prix.

## Étape 6 - Corriger la modération des avis

Fichiers concernés :

- `public/index.php`
- `app/Controllers/StaffCommon/ModerationController.php`
- `app/views/StaffCommon/moderation.view.php`
- `app/Controllers/UserController/ReviewController.php`

À faire :

- [x ] Créer ou relier une méthode pour charger les avis à modérer.
- [ x] Ajouter la route `validate-review`.
- [x ] Ajouter la route `delete-review`.
- [ x] Ajouter la route `contact-client` ou retirer ce formulaire si cette fonction n'est pas prévue.
- [x ] Vérifier que seuls l'administrateur et l'employé peuvent modérer.
- [x ] Vérifier qu'un avis validé s'affiche sur l'accueil.

Test : créer un avis de test, le valider, puis vérifier son affichage sur l'accueil.

## Étape 7 - Réactiver la gestion des utilisateurs

Fichiers concernés :

- `app/Controllers/AdminController/BanUserAdminController.php`
- `public/index.php`
- `app/views/admin/ban.admin.view.php`



Test : désactiver un compte utilisateur, tenter de se connecter avec lui, puis le réactiver.

## Étape 8 - Décider quoi faire des tickets

Fichiers concernés :

- `public/index.php`
- `app/Controllers/StaffCommon/ticketsAdminController.php`
- `app/views/StaffCommon/tickets.view.php`

Choisir une seule option :

- [ ] Option A : terminer le contrôleur et ajouter les routes `tickets` et `delete-tickets-admin`.
- [ ] Option B : retirer les liens et la vue des tickets si cette fonctionnalité n'est pas demandée pour la démonstration.

Test : vérifier qu'aucun bouton ne mène vers une route 404.

## Étape 9 - Vérifier la commande

Fichiers concernés :

- `app/Controllers/UserController/OrderMenuController.php`
- `app/views/user/order/`
- `public/index.php`

À faire :

- [ ] Tester les étapes 0 à 5 du tunnel de commande.
- [ ] Vérifier que chaque formulaire conserve `page=order-menu`.
- [ ] Vérifier que `menu_id` est transmis.
- [ ] Vérifier que les étapes 3 et 4 du contrôleur contiennent bien leur traitement.
- [ ] Vérifier que la commande est enregistrée une seule fois.
- [ ] Vérifier la redirection vers `order-success`.

Test : réaliser une commande complète avec un compte utilisateur de test.

## Étape 10 - Sécuriser toutes les actions

À faire après avoir corrigé les routes :

- [ ] Ajouter un token CSRF aux formulaires POST.
- [ ] Remplacer les suppressions et désactivations en GET par des formulaires POST.
- [ ] Vérifier les droits dans chaque contrôleur, pas uniquement dans le routeur.
- [ ] Vérifier la propriété des commandes utilisateur.
- [ ] Valider tous les identifiants reçus.
- [ ] Ne jamais afficher les erreurs SQL à l'utilisateur.

Test : tenter d'appeler directement une route admin avec un compte utilisateur et vérifier que l'accès est refusé.

## Checklist finale des routes

- [ ] Aucun lien ne contient `page=cgv` sans route dédiée.
- [ ] Aucun lien ne contient `page=password-forgotten`.
- [ ] Aucun formulaire ne contient `page=password-forgotten-send`.
- [ ] Aucun formulaire ne contient `page=updateProfil`.
- [ ] Aucune redirection ne contient `page=dashboard`.
- [ ] Les routes `add-menu-process` et `delete-menu` fonctionnent.
- [ ] Les routes `validate-review`, `delete-review` et `contact-client` fonctionnent ou sont retirées.
- [ ] Les routes employé utilisent `conflict` et `moderation`.
- [ ] Les routes `recalculer-prix-common` et `cancel-edit-order-common` sont autorisées.
- [ ] Les routes de bannissement fonctionnent.
- [ ] Les routes de tickets sont terminées ou supprimées.
- [ ] Tous les boutons ont été testés avec un compte adapté.
