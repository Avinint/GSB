<h1>Administrer les Series</h1>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('admin_serie_add');?>">Ajouter</a></p>
<table class="series-table">
<thead>
<tr>
	<td class="w5p">Id</td>
	<td class="w50p">Titre</td>
	<td class="w50p">Actions</td>
</tr>
</thead>
<tbody>
<?php  foreach($series as $serie): ?>
<tr>
	<td><?= $serie->getId(); ?></td>
	<td><?= $serie->getTitre(); ?></td>
	<td>
        <a href="<?=$this->route->generateURL('admin_serie_edit', array('id' => $serie->getId()));?>" class="btn btn-primary">Editer</a>/
        <a href="<?=$this->route->generateURL('admin_personnage_index', array('id' => $serie->getId()));?>" class="btn btn-primary">Editer Personnages</a>
        <form action="<?=$this->route->generateURL('admin_serie_delete');?>" method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $serie->id ?>">
            <button class="btn btn-danger" type="submit">Supprimer</button>
	    </form>

	</td>
</tr>
<?php  endforeach; ?>
</tbody>
</table>
