<?php

namespace App\AppModule\Controller;

use App\AppModule\Form\LogoutForm;
use Core\Controller\Controller;
use App\AppModule\Form\LoginForm;
use Core\Component\DbAuth;

class AppController extends Controller
{
    public function __construct()
    {
        parent::__construct();
      // $this->loadModel('Utilisateur');
    }

    public function login()
    {
        if(!empty($_POST) && isset($_POST['login_action'])){
            $auth = new DbAuth();

            if($user = $this->getTable('Utilisateur')->findByUsername($_POST['login_pseudo'])){
                if($auth->login($user, $_POST['login_mdp'])){

                        $this->redirect($this->route->generateURL('admin_control_panel' ));
                }
                //$route->generateRoute('admin_article_index');
            }
            echo 'Identifiants incorrects';
        }
        return new LoginForm();
        //$this->render('User:login.php', compact('form', 'error'));
    }

    public function logout()
    {
        if(!empty($_POST) && isset($_POST['logout_action'])){
                unset($_SESSION['auth']);
                unset($_SESSION['role']);
                $this->redirect($this->route->generateURL('article_index' ));
        }

        return new LogoutForm();
    }
}