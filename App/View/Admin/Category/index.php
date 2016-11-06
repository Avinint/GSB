<?php 
$app = App::getInstance();
$cats = $app->getTable('category')->all();
?>


<h1>Administrer les catégories</h1>
<p><a class= "btn btn-success" href="?p=admin.category.add">Ajouter</a></p>
<table class="table">
<thead>
<tr>
	<td>Id</td>
	<td>Nom</td>
	<td>Actions</td>
</tr>
</thead>
<tbody>

<?php  foreach($cats as $cat): ?>
<tr>
	<td><?= $cat->id ?></td>
	<td><?= $cat->name ?></td>
	<td>
	<a href="?p=admin.category.edit&id=<?= $cat->id ?>" class="btn btn-primary">Editer</a>
	<form action="?p=admin.category.delete" method ="post"style="display:inline;">
	<input type="hidden" name="id" value="<?= $cat->id ?>">
	<button class="btn btn-danger" type="submit">Supprimer</button> 
		
	</form>
	
	</td>
</tr>
<?php  endforeach; ?>
</tbody>
</table>