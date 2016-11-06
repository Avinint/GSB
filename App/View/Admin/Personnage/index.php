<h1>Administrer les Personnages de <?= $serie->getTitre(); ?></h1>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('admin_personnage_add', array('id' => $serie->getId()));?>">Ajouter</a></p>
<table class="series-table">
<thead>
<tr>
	<td>Id</td>
	<td>Nom</td>
	<td>Actions</td>
</tr>
</thead>
<tbody>

<?php  foreach($persos as $perso): ?>
<tr>
	<td><?= $perso->getId(); ?></td>
	<td><?= $perso->getNom(); ?></td>
	<td>
        <a href="<?=$this->route->generateURL('admin_personnage_edit', array('id' => $perso->getId()));?>" class="btn btn-primary">Editer</a>
        <form action="<?=$this->route->generateURL('admin_personnage_delete');?>" method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $perso->getId(); ?>">
            <button class="btn btn-danger" type="submit">Supprimer</button>
	    </form>
	</td>
</tr>
<?php  endforeach; ?>
</tbody>
</table>
