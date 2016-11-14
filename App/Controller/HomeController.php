<?php 

namespace App\Controller;

use App\Entity\Utilisateur;

class HomeController extends AppController
{
    public function index()
    {
        $app = \App::getInstance();

        $uti = $app->getTable('utilisateur')->find(1);
        var_dump($uti);
        $user = Utilisateur::find(2);
        var_dump($user);
        var_dump(Utilisateur::all());
        //$this->filterAccess('ROLE_DEFAULT');
        $this->render('Home:index.php');
    }

    public function show()
    {
        $this->render('Home:show.php');
    }

    public function __construct()
    {
        parent::__construct();
    }
}