<?php

define('ROOT', dirname(__DIR__));

require ROOT.'/App/App.php';
App::load();

$app = App::getInstance();
$db = $app->getDb();
$post = $app->getTable('post');
if(isset($_GET['p'])){
	$page = $_GET['p'];
}else{
	$page = 'post.list';
}

$auth = new Core\Auth\DbAuth($db);
if(!$auth->logged()){
	$app->forbidden();
}


ob_start();
if($page == 'post.list'){
	require ROOT.'/App/View/Admin/Post/index.php';
}else if($page == 'post.edit'){
	require  ROOT.'/App/View/Admin/Post/edit.php';
	/*$controller = new \App\Controller\PostController();
	$controller->category(); */
}else if($page == 'post.add'){
	require ROOT.'/App/View/Admin/Post/add.php';
}else if($page == 'post.delete'){
	require ROOT.'/App/View/Admin/Post/delete.php';
}else if($page == 'category.edit'){
	require  ROOT.'/App/View/Admin/Category/edit.php';
}else if($page == 'category.add'){
	require  ROOT.'/App/View/Admin/Category/add.php';
}else if($page == 'category.delete'){
	require  ROOT.'/App/View/Admin/Category/delete.php';
}else if($page == 'category.list'){
	require  ROOT.'/App/View/Admin/Category/index.php';
}else{
	require ROOT.'/App/View/Admin/Post/index.php';
	/*$controller = new \App\Controller\PostController();
	$controller->index();*/
}
$content = ob_get_clean();
require ROOT.'\App\View\Template\default.php'
?>
<html>
<head>
<title><?=$app->title ?></title>
</head>
<body>
	
</body>
</html>
