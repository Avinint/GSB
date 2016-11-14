<?php

$app = App::getInstance();
$post = $app->getTable('post')->find($_GET['id']);
if ($post === false){
	$app->notFound();
}

?>
<h1><?=$post->title; ?></h1>
<p><em><?=$post->category ?></em></p>
<p><?=$post->content; ?></p>