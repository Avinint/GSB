<?php
define('ROOT', dirname(__DIR__));
require_once ROOT.'/App/Config/globals.php';
require_once ROOT.'/App/App.php';

App::load();
$app = App::getInstance('prod');
$app->getRouter()->getRoute();