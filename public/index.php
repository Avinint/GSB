<?php

define('ROOT', dirname(__DIR__));
require ROOT.'/App/App.php';



App::load();
$app = App::getInstance();
//throw new Exception("bla bla bla");
$controller = $app->getRouting()->resolveRoute();

// TODO  resolveRoute renvoie la route pour acces dans le controller