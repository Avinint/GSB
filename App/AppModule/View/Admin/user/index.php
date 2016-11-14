<h1>Administrer les utilisateurs</h1>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('admin_utilisateur_add');?>">Ajouter</a></p>
<table class="uti-table">
<thead>
<tr>
	<td>Id</td>
	<td>Pseudo</td>
    <td>Prénom</td>
    <td>Nom</td>
    <td>Email</td>
    <td>Avatar</td>
    <td>Newsletter</td>
    <td>Role</td>
	<td>Actions</td>
</tr>
</thead>
<tbody>
<?php //var_dump($_SERVER['REQUEST_URI']);?>
<?php  foreach($utis as $uti): ?>
<tr>
	<td><?= $uti->id ?></td>
	<td><?= $uti->pseudo ?></td>
    <td><?= $uti->prenom ?></td>
    <td><?= $uti->nom ?></td>
    <td><?= $uti->email ?></td>
    <td><?= $uti->image ?></td>
    <td><?= $uti->newsletter ?></td>
    <td><?= $uti->role_id ?></td>
	<td>
        <a href="<?=$this->route->generateURL('admin_utilisateur_edit', array('id' => $uti->id));?>" class="btn btn-primary">Editer</a>
        <form action="<?=$this->route->generateURL('admin_utilisateur_delete');?>" method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $uti->id ?>">
            <button class="btn btn-danger" type="submit">Supprimer</button>
	    </form>
	</td>
</tr>
<?php  endforeach; ?>
</tbody>
</table>
