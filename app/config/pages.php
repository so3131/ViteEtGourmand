<?php
// Fichier de configuration des pages accessibles selon les rôles

return [
    // Pages accessibles à tous les utilisateurs, y compris les visiteurs non connectés
    'publiques' => [
        'home',
        'login',
        'signin',
        'search',
        'details-menu',
        'filter',
        'contact',
        'contact-success',
        'mention',
        '404',
        'error-ban',
        'forgot-password',
        'reset-password'
    ],
    // Toutes les pages existantes dans l'application, indépendamment des rôles
    'existante' => [
        'home',
        'login',
        'signin',
        'logout',
        'search',
        'details-menu',
        'order-menu',
        'order-success',
        'ajax-frais-livraison',
        'filter',
        'contact',
        'contact-success',
        'mention',
        '404',
        'error-ban',
        'ban-user',
        'unban-user',
        'forgot-password',
        'reset-password',
        'update-profil',
        'edit-order',
        'recalculer-prix',
        'recalculer-prix-common',
        'cancel-edit-order',
        'update-order',
        'dashboard-user',
        'review',
        'store-review',

        'dashboard-employee',

        'dashboard-admin',
        'stats-admin',
        'rh-admin',
        'rh-admin-create',
        'rh-admin-delete',
        'rh-admin-toggle',
        'ban-user-admin',
        'ban-action-admin',
        'unban-action-admin',
        'ban-user',
        'unban-user',



        'menu-management',
        'order-management',
        'cancel-order-common',
        'edit-order-common',
        'create-plat-ajax',
        'update-order-common',
        'update-order-status',
        'edit-menu',
        'update-menu-process',
        'add-menu-process',
        'delete-menu',
        'activate-menu',
        'delete-plat',
        'activate-plat',
        'add-plat-process',
        'erase-order',
        'review-management',
        'update-review-status',
        'contact-material-client'
    ],
    // Pages accessibles selon les rôles

    // Role propre à l'Admin
    'admin' => [
        'dashboard-admin',
        'stats-admin',
        'rh-admin',
        'rh-admin-create',
        'rh-admin-delete',
        'rh-admin-toggle',
        'ban-user-admin',
        'ban-action-admin',
        'unban-action-admin',
        'ban-user',
        'unban-user',
    ],
    // Role propre a l'Employee
    'employee' => [
        'dashboard-employee',

    ],
    // Role propre auStaff = Employee + Admin
    'staff' => [

        
        'menu-management',
        'order-management',
        'cancel-order-common',
        'edit-order-common',
        'update-order-common',
        'update-order-status',
        'edit-menu',
        'update-menu-process',
        'add-menu-process',
        'create-plat-ajax',
        'delete-menu',
        'delete-plat',
        'activate-plat',
        'add-plat-process',
        'activate-menu',
        'review-management',
        'update-review-status',
        'recalculer-prix-common',
        'error-ban',
        'contact-material-client'
    ],
    // Role propre à l'User
    'user' => [
        'dashboard-user',
        'update-profil',
        'order-menu',
        'order-success',
        'erase-order',
        'ajax-frais-livraison',
        'edit-order',
        'recalculer-prix',
        'cancel-edit-order',
        'update-order',
        'review',
        'store-review',
        'recalculer-prix'
    ],
];
