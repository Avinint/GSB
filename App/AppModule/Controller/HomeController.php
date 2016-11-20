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