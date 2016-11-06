<?php 
$catTable = App::getInstance()->getTable('category');

if(!empty($_POST)){
	$result = $catTable->delete($_POST['id']);
	header('Location: admin.php?p=category.list');	
}

?>
