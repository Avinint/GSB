<?php 

namespace App\AppModule\Controller;


class ArticleController extends Controller
{
	public function index()
	{
		$articles = $this->getTable('AppModule:Article')->findAll();
		
		$this->render('Home/article.php', array('articles' => $articles);
		
	}
}