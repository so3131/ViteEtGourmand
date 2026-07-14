<?php
// app/config/pages.php

return [
    // 1. Tableaux de pages : publics, privés, et tous les noms de pages valides pour la sécurité
// Pages publiques, accessibles à tous, même sans être connecté
'publiques' =>['home', 'login', 'signin', 'search', 'details-menu', 'filter', 'contact', 'contact-success', 'mention', '404', 'error-ban', 'forgot-password', 'reset-password'],
// Toutes les pages qui existent, pour éviter les injections de page
'existante' => ['home', 'login', 'signin', 'logout', 'search', 'details-menu', 'order-menu', 'order-success','ajax-frais-livraison', 'filter', 'contact', 'contact-success', 'mention', '404', 'error-ban', 'forgot-password', 'reset-password', 'update-profil', 'edit-order','recalculer-prix','cancel-edit-order','update-order', 'dashboard-user', 'erase-order', 'dashboard-employee', 'conflict-employee', 'moderation-employee', 'dashboard-admin', 'stats-admin', 'rh-admin', 'ban-user-admin', 'ban-action-admin', 'moderation-admin', 'conflict-admin', 'tickets-admin', 'delete-tickets-admin', 'unban-action-admin'],

// 2. On trie les pages privées par rôle pour le contrôle d'accès
// Admin
'admin' => ['dashboard-admin', 'stats-admin', 'rh-admin', 'ban-user-admin', 'ban-action-admin', 'moderation-admin', 'conflict-admin', 'tickets-admin', 'delete-tickets-admin', 'unban-action-admin'],
// Employé
'employee' => ['dashboard-employee', 'conflict-employee', 'moderation-employee'],
// Utilisateur
'user' => ['dashboard-user', 'update-profil',  'order-menu', 'order-success','erase-order','ajax-frais-livraison', 'edit-order','recalculer-prix','cancel-edit-order','update-order'],

// update-profil action qui se trouve dans le dashboard-user, mais comme   déclenchées par un formulaire POST,  doit être traitée à part dans le routage, pour éviter les conflits avec l'affichage du dashboard qui lui est en GET. 
];