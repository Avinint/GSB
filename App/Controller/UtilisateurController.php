<?php 

namespace App\Controller;

use App\Form\LoginForm;
use App\Form\ProfilForm;
use App\Entity\Utilisateur;
use App\Form\ContactForm;
use App\Form\InscriptionForm;
use Core\Auth\DbAuth;

class UtilisateurController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $this->loadModel('Article');
        $this->loadModel('Utilisateur');
    }

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
            $user =  $this->Utilisateur->findNoPassword($_SESSION['auth']);
        }
        $login = $this->login();
        $headlines = $this->Article->extract('id', 'titre');
        $page = 'Contactez nous:';
        $form = new ContactForm();
        $this->render('User:contact.php', compact('form','page', 'login', 'logout', 'User', 'headlines'));
    }

    public function signup()
    {
        $error = false;
        $form = new InscriptionForm();
        if(!empty($_POST) && $_POST['signup_action'] == 'signup'){

            $auth = new DBAuth();

            if($this->Utilisateur->valueAvailable('pseudo', $_POST['signup_pseudo'])){

                if($_POST['signup_mdp'] === $_POST['signup_mdpConf']){

                    $_POST['signup_mdp'] = hash('sha512', $_POST['signup_mdp']);
                    $user = new Utilisateur();
                    //unset($_POST['signup_mdp_conf']);

                    $object = array('entity' => $user,
                        'login' => true,
                        'children' => array(
                        ));

                    $this->handleRequest($form, $object, $this->route->generateURL('utilisateur_profil_edit'));
                }
            }

            }else{
                $error = 'identifiants d\'inscription non corrects';
            }


        $headlines = $this->Article->extract('id', 'titre');

        $login = $this->login();
        $page = 'Inscription:';

        $this->render('User:inscription.php', compact('form','page', 'login', 'headlines'));
    }
}