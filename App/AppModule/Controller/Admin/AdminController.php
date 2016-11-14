<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Core\Component\DbAuth;
use\App;


class AdminController extends AppController
{
	public function __construct()
	{
		parent::__construct();
		$app = App::getInstance();
		$auth = new DbAuth();
		if(!$auth->logged()){
			$this->forbidden();
		}
        $this->loadModel('Article');
        $this->loadModel('Serie');
        $this->loadModel('Commentaire');
	}

    public function panel()
    {
        $logout = $this->logout();
        $user =  $this->Utilisateur->findNoPassword($_SESSION['auth']);

        $articles = array();
        $sorties = array();
        $series = array();

        if($user->role_id > 0){
            $articles = $this->Article->articlesParAuteur($_SESSION['auth']);
            $sorties =  $this->Article->sortiesParAuteur($_SESSION['auth']);
            $series =  $this->Serie->listeParAuteur($_SESSION['auth']);
        }
       $commentaires = $this->Commentaire->listeParAuteur($_SESSION['auth']);

        $this->render('Admin:Admin:panel.php', array(
                'logout' => $logout,
                'articles' => $articles,
                'sorties' => $sorties,
                'series' => $series,
                'commentaires' => $commentaires,
                'User' => $user,
            ));
    }
}