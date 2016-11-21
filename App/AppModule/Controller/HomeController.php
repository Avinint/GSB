<?php 

namespace App\AppModule\Controller;

use App\AppModule\Entity\Utilisateur;

class HomeController extends AppController
{
    public function index()
    {
        $app = \App::getInstance();
        $user = $app->getTable('AppModule:Utilisateur')->find(1);
        var_dump($user);
       
		//$utis = $app->getTable('utilisateur')->all();
		//$users = Utilisateur::findByVille('Paris');
        /* echo $uti->prenom.' '.$uti->nom.BR;
        $user = Utilisateur::find(2);
        echo $user->prenom.' '.$uti->nom;
        var_dump('jjje');*/
        //var_dump(Utilisateur::all());
        //$this->filterAccess('ROLE_DEFAULT');
        $this->render('AppModule:Home:index.php');
    }

    public function show()
    {
        $this->render('AppModule:Home:show.php');
    }

    public function __construct()
    {
        parent::__construct();
    }
}