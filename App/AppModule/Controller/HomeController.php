<?php 

namespace App\AppModule\Controller;

use App\AppModule\Entity\Utilisateur;

class HomeController extends AppController
{
    public function index()
    {
        $app = \App::getInstance();
		
        //$uti = $app->getTable('AppModule:Utilisateur')->find(1);
       // var_dump($uti);
		//$utis = $app->getTable('utilisateur')->all();
		$user = $app->getTable('AppModule:Utilisateur')->findOneBy(array('prenom' => 'Haitem'));
        var_dump($user);
        //var_dump($users);
        /* echo $uti->prenom.' '.$uti->nom.BR;
        $user = Utilisateur::find(2);
        echo $user->prenom.' '.$uti->nom;
        */
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