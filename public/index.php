﻿<?php

define('ROOT', dirname(__DIR__));
require ROOT.'/App/App.php';
require ROOT.'/App/Config/globals.php';

var_dump( $_SERVER['REQUEST_URI']);
var_dump( $_SERVER['HTTP_HOST']);
App::load();
$app = App::getInstance('dev');

$controller = $app->getRouting()->resolveRoute();

// TODO  resolveRoute renvoie la route pour acces dans le controller