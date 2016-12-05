<?php 

namespace App\AppModule\Controller;

use App\AppModule\Form\LoginForm;
use App\AppModule\Form\ProfilForm;
use App\AppModule\Form\UtilisateurForm;
use App\AppModule\Form\UtilisateurAddForm;
use App\AppModule\Entity\Utilisateur;
use App\AppModule\Form\ContactForm;
use App\AppModule\Form\InscriptionForm;

class BackOfficeController extends AppController
{
    public function index()
    {
        $logout = $this->logout();
        $user =  $this->getTable('AppBundle:Utilisateur')->findNoPassword($_SESSION['auth']);

        $utis = $this->getTable('AppModule:Utilisateur')->findAll();
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
        $user =  $this->getTable('AppModule:Utilisateur')->findNoPassword($_SESSION['auth']);
        $uti =  $this->getTable('AppModule:Utilisateur')->find($id);
        $form = new UtilisateurForm($this->generateURL('admin_utilisateur_edit', array(
                'id' => $id,
            )), $uti);

        if(!empty($_POST) && $_POST['uti_action'] == 'uti'){
            if($_POST['uti_mdp'] !== ''){
                //$_POST['uti_mdp'] = hash('sha512', $_POST['uti_mdp']);
                //$_POST['uti_mdpConf'] = hash('sha512', $_POST['uti_mdpConf']);
            }

            $data = array(
                'entity' => $uti,
                'fk' => array('role' => 'role_id')
                );

            $this->handleRequest($data, $object, $this->generateURL('admin_utilisateur_index'));
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
        $user =  $this->getTable('AppModule:Utilisateur')->findNoPassword($_SESSION['auth']);
        $uti =  new Utilisateur();
        $form = new UtilisateurAddForm($this->generateURL('admin_utilisateur_add'
            ), $uti);

        if(!empty($_POST) && $_POST['uti_action'] == 'uti'){
            if($_POST['uti_mdp'] !== ''){
               // $_POST['uti_mdp'] = hash('sha512', $_POST['uti_mdp']);
              //  $_POST['uti_mdpConf'] = hash('sha512', $_POST['uti_mdpConf']);
            }

            $data = array(
                'entity' => $uti,
                'fk' => array('role' => 'role_id')
            );

            $this->handleRequest($form, $data, $this->generateURL('admin_utilisateur_index'));
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
            if($this->getTable('AppModule:Utilisateur')->delete($_POST['id'])){
                $this->redirect($this->generateURL('admin_utilisateur_index'));
            }
        }
    }

    
}