 <h1 class="page-title"><?=$page.' '.$user->getLogin(); ?></h1>
 
<?=$form->createView(array('submit' => 'Modifier', 'action' => $this->url('utilisateur_compte'))) ?>
