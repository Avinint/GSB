<?php

return array(
    'utilisateur_login' => array(
        'path' => '/login',
        'controller' => 'AppModule:AppController:login',
    ),
    'utilisateur_logout' => array(
        'path' => '/logout',
        'controller' => 'AppModule:AppController:logout',
    ),
    'homepage' => array(
        'path' => '/',
        'controller' => 'AppModule:HomeController:index',
    ),
    'yoyopage' => array(
        'path' => '/yoyo',
        'controller' => 'AppModule:HomeController:show',
    ),
    'yoyolalapage' => array(
        'path' => '/yoyo/lala',
        'controller' => 'AppModule:HomeController:show',
    ),
    'admin_control_panel' => array(
        'path' => '/control/panel',
        'controller' => 'AppModule:Admin:AdminController:panel',
    ),
    'utilisateur_contact' => array(
        'path' => '/contact',
        'controller' => 'AppModule:UtilisateurController:contact',
    ),
    'utilisateur_signup' => array(
        'path' => '/inscription',
        'controller' => 'AppModule:UtilisateurController:signup',
    ),
    'utilisateur_profil_edit' => array(
        'path' => '/profil/edit',
        'controller' => 'AppModule:Admin:UtilisateurController:editProfil',
    ),
    'admin_utilisateur_index' => array(
        'path' => '/admin/utilisateur/index',
        'controller' => 'AppModule:Admin:UtilisateurController:index',
    ),
    'admin_utilisateur_edit' => array(
        'path' => '/admin/utilisateur/edit/{id}',
        'controller' => 'AppModule:Admin:UtilisateurController:edit',
    ),
    'admin_utilisateur_add' => array(
        'path' => '/admin/utilisateur/add',
        'controller' => 'AppModule:Admin:UtilisateurController:add',
    ),
    'admin_utilisateur_delete' => array(
        'path' => '/admin/utilisateur/delete',
        'controller' => 'AppModule:Admin:UtilisateurController:delete',
    ),
   
);