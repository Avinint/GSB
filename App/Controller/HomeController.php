<?php 

namespace App\Controller;

class HomeController extends AppController
{
    public function index()
    {
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