<?php
if($error):
?>
	<div class="alert alert-danger">
		Identifiants incorrects
	</div>
<?php
endif;
?>
<form method="post">
<?=$form->input('login', 'Login'); ?>
<?=$form->password('mdp', 'Mot de passe'); ?>
<?=$form->submit('connexion'); ?>
</form>
