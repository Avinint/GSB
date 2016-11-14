<?php 

namespace App\Controller;

class HomeController extends AppController
{
    public function index()
    {
        $name = "Jean Jean";
        $this->render('Home:index.php', array(
                'name' => $name
            ));
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