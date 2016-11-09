<?php

use Core\Auth\DbAuth;

define('ROOT', dirname(__DIR__));
define('D_S', DIRECTORY_SEPARATOR);
define('BR', '<br/>');

require ROOT.'/App/App.php';

App::load();
 
$app = App::getInstance();
$controller = $app->getRouting()->resolveRoute();

// TODO  resolveRoute renvoie la route pour acces dans le controller