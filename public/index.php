<?php
var_dump(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
define('ROOT', dirname(__DIR__));
require_once ROOT.'/App/Config/globals.php';
require_once ROOT.'/App/App.php';

App::load();
$app = App::getInstance('prod');
$app->getRouter()->getRoute();