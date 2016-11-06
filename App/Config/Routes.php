<?php

return array(
    'utilisateur_login' => array(
        'path' => '/login',
        'controller' => 'AppController:login',
    ),
    'utilisateur_logout' => array(
        'path' => '/logout',
        'controller' => 'AppController:logout',
    ),
    'homepage' => array(
        'path' => '/',
        'controller' => 'HomeController:index',
    ),
    'admin_control_panel' => array(
        'path' => '/control/panel',
        'controller' => 'Admin:AdminController:panel',
    ),
    'utilisateur_contact' => array(
        'path' => '/contact',
        'controller' => 'UtilisateurController:contact',
    ),
    'utilisateur_signup' => array(
        'path' => '/inscription',
        'controller' => 'UtilisateurController:signup',
    ),
    'utilisateur_profil_edit' => array(
        'path' => '/profil/edit',
        'controller' => 'Admin:UtilisateurController:editProfil',
    ),
    'admin_utilisateur_index' => array(
        'path' => '/admin/utilisateur/index',
        'controller' => 'Admin:UtilisateurController:index',
    ),
    'admin_utilisateur_edit' => array(
        'path' => '/admin/utilisateur/edit/{id}',
        'controller' => 'Admin:UtilisateurController:edit',
    ),
    'admin_utilisateur_add' => array(
        'path' => '/admin/utilisateur/add',
        'controller' => 'Admin:UtilisateurController:add',
    ),
    'admin_utilisateur_delete' => array(
        'path' => '/admin/utilisateur/delete',
        'controller' => 'Admin:UtilisateurController:delete',
    ),
   
);