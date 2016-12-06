<?php 

namespace App\AppModule\Controller;

use App\AppModule\Form\LoginForm;
use App\AppModule\Form\CompteForm;
use App\AppModule\Entity\Utilisateur;
use App\AppModule\Form\ContactForm;
use App\AppModule\Form\InscriptionForm;
use Core\Component\Auth;

class UtilisateurController extends AppController
{
    /*public function __construct()
    {
        parent::__construct();
        $this->loadModel('Article');
        $this->loadModel('Utilisateur');
    }*/

    public function login()
    {
        $error = false;
        if (!empty($_POST)) {
            $auth = $this->container['auth'];

            if ($auth->login($this->getTable('AppModule:Utilisateur')->findByUsername($_POST['login']['login']),
                    $_POST['login']['mdp'])) {
                $this->redirect($this->generateURL('utilisateur_compte_edit'));
            }else{
                $error = true;
            }
        }
        $form = new LoginForm();

        $this->render('AppModule:User:login.php', compact('form', 'error'));
    }

    public function contact()
    {
        $logout = $this->logout();
        if(isset($_SESSION['auth'])){
            $user =  $this->getTable('AppModule:Utilisateur')->findNoPassword($_SESSION['auth']);
        }
        $login = $this->login();
        $headlines = $this->getTable('AppModule:Article')->extract('id', 'titre');
        $page = 'Contactez nous:';
        $form = new ContactForm();
        $this->render('User:contact.php', array('form'=> $form,'page'=> $page, 'login'=> $login, 'logout'=> $logout, 'User'=>$user, 'headlines'=> $headlines));
    }

    public function signup()
    {
        $user = new Utilisateur();
        $form = new InscriptionForm($user);
        if (!empty($_POST) && $_POST['signup']['action'] == 'signup') {

            $auth = $this->container['auth'];
            if ($this->getTable('AppModule:Utilisateur')->valueAvailable('login', $_POST['signup']['login']) &&
                $this->getTable('AppModule:Utilisateur')->valueAvailable('email', $_POST['signup']['email'])) {
                    $form->handleRequest();

                if ($form->isValid()) {

                    $this->save($user);
                    $auth->authenticate($user);
                    $this->redirect($this->generateURL('utilisateur_compte_edit'));
                }
            } else {
                echo "Les identifiants choisis existent déja";
            }
        }

        $page = 'Inscription:';

        $this->render('AppModule:User:inscription.php', compact('form','page'), 'no_template');
    }

    public function editCompte()
    {
        $this->filterAccess('ROLE_USER');
        $logout = $this->logout();
        $user 	= $this->getTable('AppModule:Utilisateur')->findNoPassword($_SESSION['auth']);
        $form   = new CompteForm($user,$this->generateURL('utilisateur_compte_edit'));

        if (!empty($_POST) && $_POST['compte']['action'] == 'editCompte') {
            $form->handleRequest($user);

            if ($form->isValid()) {
                    $this->save($user);
                var_dump("save");
                    $this->redirect($this->generateURL('utilisateur_compte_edit'));
            }
        }

        //$login = $this->login();
        $page = 'gestionnaire de compte utilisateur:';

        $this->render('AppModule:User:compte.php', array(
                'form'   => $form,
                'logout' => $logout,
                'page'   => $page,
                'user' => $user,
            ));
    }
}