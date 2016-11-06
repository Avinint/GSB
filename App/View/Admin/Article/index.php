<h1>Administrer les articles</h1>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('admin_article_add');?>">Ajouter</a></p>
<table class="article-table">
<thead>
<tr>
	<td>Id</td>
	<td class="w30p">Titre</td>
	<td class="w70p tac">Actions</td>
</tr>
</thead>
<tbody>
<?php //var_dump($_SERVER['REQUEST_URI']);?>
<?php  foreach($articles as $article): ?>
<tr>
	<td><?= $article->id ?></td>
	<td><?= $article->titre ?></td>
	<td class ="tar">
        <a href="<?=$this->route->generateURL('admin_article_edit', array('id' => $article->id));?>" class="btn btn-primary">Editer</a>
        <a href="<?=$this->route->generateURL('admin_commentaire_index', array('id' => $article->id));?>" class="btn btn-primary">Editer commentaires</a>
        <form action="<?=$this->route->generateURL('admin_article_delete');?>" method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $article->id ?>">
            <button class="btn btn-danger" type="submit">Supprimer</button>
	    </form>
	</td>
</tr>
<?php  endforeach; ?>
</tbody>
</table>

&nbsp;
<h1>Administrer le calendrier</h1>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('admin_sortie_add');?>">Ajouter</a></p>
    <table class="article-sorties">
        <thead>
        <tr>
            <td>Id</td>
            <td class="w30p">Titre</td>
            <td class="w70p tac">Actions</td>
        </tr>
        </thead>
        <tbody>

<?php  foreach($sorties as $article): ?>
<tr>
    <td><?= $article->getId();?></td>
    <td><?= $article->titre; ?></td>
    <td class="tar">
        <a href="<?=$this->route->generateURL('admin_sortie_edit', array('id' => $article->id));?>" class="btn btn-primary">Editer</a>
        <a href="<?=$this->route->generateURL('admin_commentaire_index', array('id' => $article->id));?>" class="btn btn-primary">Editer commentaires</a>
        <form action="<?=$this->route->generateURL('admin_article_delete');?>" method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $article->id ?>">
            <button class="btn btn-danger" type="submit">Supprimer</button>
        </form>
    </td>
</tr>
<?php  endforeach; ?>
</tbody>
</table>