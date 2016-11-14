<?php 

namespace App\Controller\Admin;

use Core\Form\BootstrapForm;

class CategoryController extends AdminController
{
	/**
	public function __construct()
	{
		parent::__construct();
		$this->loadModel('Article');
		$this->loadModel('Category');
	}
	
	public function index()
	{
		$categories = $this->Category->all();
		$this->render('Admin:Category:index.php', compact('categories'));
		
	}
	
	public function add()
	{
		if(!empty($_POST)){
			$result = $this->Category->create(
				[
					'name' => $_POST['name'],
				]
			);
			if($result){
				return $this->index();
			}
		}
		$form = new BootstrapForm();		
		
		$this->render('Admin:Category:edit.php', compact('form'));
	}
	
	public function edit()
	{
		if(!empty($_POST)){
			$result = $this->Category->update(
				$_GET['id'], [
					'name' => $_POST['name'],
				]
			);
			if($result){
				return $this->index();
				//echo '<div class="alert alert-success">L\'article a bien été mis à jour</div>';		
			}		
		}
		$cat = $this->Category->find($_GET['id']);
		$form = new BootstrapForm($cat);
		
		 $this->render('Admin:Category:edit.php', compact('form'));
	}
	
	public function delete()
	{
		if(!empty($_POST)){
			$result = $this->Category->delete($_POST['id']);
			return $this->index();	
		}
	}
	
	public function getLastInsertId()
	{
		return \App::getInstance()->getDb()->lastInsertId();
	}
	**/
}