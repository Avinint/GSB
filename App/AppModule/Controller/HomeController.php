<?php 

namespace App\AppModule\Controller;

use App\AppModule\Entity\Utilisateur;

class HomeController extends AppController
{
    public function index()
    {
		$user = new Utilisateur();
		
		//$this->container['tool']->debug($user->getMetadata());
		$user->getTableName();
		
		//echo password_hash('riveton', PASSWORD_BCRYPT);if(!function_exists('hash_equals'))


       // $user = $app->getTable('AppModule:Utilisateur')->find(1);
		//$utis = $app->getTable('utilisateur')->all();
		
        /* echo $uti->prenom.' '.$uti->nom.BR;
        $user = Utilisateur::find(2);
        echo $user->prenom.' '.$uti->nom;*/
        //var_dump(Utilisateur::findByPrenom('Bruno', array('Prenom', "ASC"), 0 , 1));
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