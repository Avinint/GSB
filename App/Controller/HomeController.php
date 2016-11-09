<?php 

namespace App\Controller;

class HomeController extends AppController
{
    public function index()
    {
        $this->render('Home:index.php');
    }

    public function __construct()
    {
        parent::__construct();
    }
}