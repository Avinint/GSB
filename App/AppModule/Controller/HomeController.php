<?php 

namespace App\AppModule\Controller;

use App\AppModule\Entity\Utilisateur;

class HomeController extends AppController
{
    public function index()
    {
        $app = \App::getInstance();

        $uti = $app->getTable('utilisateur')->find('a131');
		//$utis = $app->getTable('utilisateur')->all();
		//$users = Utilisateur::findByVille('Paris');
        echo $uti->prenom.' '.$uti->nom.BR;
        $user = Utilisateur::find('d13');
        echo $user->prenom.' '.$uti->nom;
        //var_dump(Utilisateur::all());
        //$this->filterAccess('ROLE_DEFAULT');
        $this->render('App:Home:index.php');
    }

    public function show()
    {
        $this->render('App:Home:show.php');
    }

    public function __construct()
    {
        parent::__construct();
    }
}