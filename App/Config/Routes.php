<?php

return array(
    'utilisateur_login' => array(
        'path' => '/login',
        'controller' => 'AppModule:UtilisateurController:login',
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
        'controller' => 'AppModule:BackOfficeController:editProfil',
    ),
    'utilisateur_index' => array(
        'path' => '/admin/utilisateur/index',
        'controller' => 'AppModule:BackOfficeController:index',
    ),
    'utilisateur_edit' => array(
        'path' => '/admin/utilisateur/edit/{id}',
        'controller' => 'AppModule:BackOfficeController:edit',
    ),
    'utilisateur_add' => array(
        'path' => '/admin/utilisateur/add',
        'controller' => 'AppModule:BackOfficeController:add',
    ),
    'utilisateur_delete' => array(
        'path' => '/admin/utilisateur/delete',
        'controller' => 'AppModule:BackOfficeController:delete',
    ),
   
);