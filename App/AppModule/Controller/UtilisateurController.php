<?php 

namespace App\AppModule\Controller;

use App\AppModule\Form\LoginForm;
use App\AppModule\Form\ProfilForm;
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

    /*public function login()
    {
        $error = false;
        if(!empty($_POST)){
            $auth = new DBAuth(\App::getInstance()->getDb());
            var_dump($this->Utilisateur->findByUsername($_POST['pseudo']));
            if($auth->login($this->Utilisateur->findByUsername($_POST['pseudo']), $_POST['mdp'])){

            $this->redirect($this->route->generateURL('admin_article_index' ));

            }else{
                $error = true;
            }
        }
        $form = new LoginForm();

        $this->render('User:login.php', compact('form', 'error'));
    }*/

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
        $error = false;
        $form = new InscriptionForm();
        if (!empty($_POST) && $_POST['signup_action'] == 'signup') {

            $auth = $this->container['auth'];

            if ($this->getTable('AppModule:Utilisateur')->valueAvailable('pseudo', $_POST['signup_pseudo']) &&
                $this->getTable('AppModule:Utilisateur')->valueAvailable('email', $_POST['signup_pseudo'])
            ) {
                // if ($_POST['signup_mdp'] === $_POST['signup_mdpConf']) {
                    //$_POST['signup_mdp'] =  password_hash($_POST['signup_mdp'], PASSWORD_BCRYPT );
                    $user = new Utilisateur();
                    //unset($_POST['signup_mdp_conf']);

                    $data = array('entity' => $user,
                        'login' => true,
                        'children' => array(
                        ));

                    $this->handleRequest($form, $data, $this->generateURL('utilisateur_profil_edit'));
               //}
            }
        } else {
            $error = 'identifiants d\'inscription non corrects';
        }


       // $headlines = $this->getTable('AppModule:Utilisateur')->extract('id', 'titre');

       // $login = $this->login();
        $page = 'Inscription:';

        $this->render('AppModule:User:inscription.php', compact('form','page'), 'no_template');
    }
}