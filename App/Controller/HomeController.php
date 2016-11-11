<?php 

namespace App\Controller;

class HomeController extends AppController
{
    public function index()
    {
        throw new \Exception("bla bla bla");echo "holala";
        $this->render('Home:index.php');
    }

    public function __construct()
    {
        parent::__construct();
    }
}