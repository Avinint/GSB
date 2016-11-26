<?php 

namespace App\Controller\Admin;

use App\Form\LoginForm;
use App\Form\ProfilForm;
use App\Form\UtilisateurForm;
use App\Form\UtilisateurAddForm;
use App\Entity\Utilisateur;
use App\Form\ContactForm;
use App\Form\InscriptionForm;
use Core\Auth\DbAuth;

class UtilisateurController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->loadModel('Article');
        $this->loadModel('Utilisateur');
    }

    public function index()
    {
        $logout = $this->logout();
        $user =  $this->Utilisateur->findNoPassword($_SESSION['auth']);

        $utis = $this->Utilisateur->findAll();
        $this->render('Admin:User:index.php', array(
                'logout' => $logout,
                'utis' => $utis,
                'User' => $user,
            ));
    }

    public function edit($id)
    {
        extract($id);
        $logout = $this->logout();
        $user =  $this->Utilisateur->findNoPassword($_SESSION['auth']);
        $uti =  $this->Utilisateur->find($id);
        $form = new UtilisateurForm($this->route->generateURL('admin_utilisateur_edit', array(
                'id' => $id,
            )), $uti);

        if(!empty($_POST) && $_POST['uti_action'] == 'uti'){
            if($_POST['uti_mdp'] !== ''){
                //$_POST['uti_mdp'] = hash('sha512', $_POST['uti_mdp']);
                //$_POST['uti_mdpConf'] = hash('sha512', $_POST['uti_mdpConf']);
            }

            $object = array(
                'entity' => $uti,
                'fk' => array('role' => 'role_id')
                );

            $this->handleRequest($form, $object, $this->route->generateURL('admin_utilisateur_index'));
        }

        $this->render('Admin:User:edit.php', array(
                'form' => $form,
                'logout' => $logout,
                'User' => $user,
                'id' => $id,
            ));
    }

    public function add()
    {
        $logout = $this->logout();
        $user =  $this->Utilisateur->findNoPassword($_SESSION['auth']);
        $uti =  new Utilisateur();
        $form = new UtilisateurAddForm($this->route->generateURL('admin_utilisateur_add'
            ), $uti);

        if(!empty($_POST) && $_POST['uti_action'] == 'uti'){
            if($_POST['uti_mdp'] !== ''){
               // $_POST['uti_mdp'] = hash('sha512', $_POST['uti_mdp']);
              //  $_POST['uti_mdpConf'] = hash('sha512', $_POST['uti_mdpConf']);
            }

            $object = array(
                'entity' => $uti,
                'fk' => array('role' => 'role_id')
            );

            $this->handleRequest($form, $object, $this->route->generateURL('admin_utilisateur_index'));
        }

        $this->render('Admin:Article:add.php', array(
                'form' => $form,
                'logout' => $logout,
                'User' => $user,
            ));
    }

    public function delete()
    {
        if(!empty($_POST)){
            if($this->Utilisateur->delete($_POST['id'])){
                $this->redirect($this->route->generateURL('admin_utilisateur_index'));
            }
        }
    }

    public function editProfil()
    {
        $logout = $this->logout();
        $user =  $this->Utilisateur->findNoPassword($_SESSION['auth']);

        $form = new ProfilForm($this->route->generateURL('utilisateur_profil_edit'), $user);

        if(!empty($_POST) && $_POST['profil_action'] == 'editProfil'){

            if($_POST['profil_mdp'] !== ''){
               // $_POST['profil_mdp'] = hash('sha512', $_POST['profil_mdp']);
                //$_POST['profil_mdpConf'] = hash('sha512', $_POST['profil_mdpConf']);
            }
            $object = array('entity' => $user);

            $this->handleRequest($form, $object, $this->route->generateURL('admin_control_panel'));
        }

        $headlines = $this->Article->extract('id', 'titre');

        //$login = $this->login();
        $page = 'Mettre à jour mon profil:';

        $this->render('User:profil.php', array(
                'form' => $form,
                'logout' => $logout,
                'page' => $page,
                'User' => $user,
                'headlines' => $headlines,
            ));
    }
}