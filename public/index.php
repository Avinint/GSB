<?php

define('ROOT', dirname(__DIR__));
require ROOT.'/App/App.php';

try{
    throw new Exception("bla bla bla");
    echo "hll;o";
}catch(Exception $e){
    echo "hererer;o";
    var_dump($e);

    echo $e->getMessage();
}

App::load();
$app = App::getInstance();
$controller = $app->getRouting()->resolveRoute();

// TODO  resolveRoute renvoie la route pour acces dans le controller