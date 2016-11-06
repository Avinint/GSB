<h1>Administrer les commentaires</h1>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('admin_commentaire_add', array('id' => $article->getId()));?>">Ajouter</a></p>
<table class="article-table">
    <thead>
    <tr>
        <td>Id</td>
        <td class="w20p">Auteur</td>
        <td class="w30p tl">Extrait</td>
        <td class="w25p">Date</td>
        <td class="w25p">Actions</td>
    </tr>
    </thead>
    <tbody>
    <?php //var_dump($_SERVER['REQUEST_URI']);?>
    <?php  foreach($comments as $comment): ?>
        <tr>
            <td><?= $comment->getId() ?></td>
            <td><?= $comment->pseudo ?></td>
            <td><?= $this->shorten($comment->contenu)?> </td>
            <td><?= $comment->date ?></td>
            <td>
                <a href="<?=$this->route->generateURL('admin_commentaire_edit', array('id' => $comment->getId()));?>" class="btn btn-primary">Editer</a>
                <form action="<?=$this->route->generateURL('admin_commentaire_delete');?>" method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $comment->getId() ?>">
                    <button class="btn btn-danger" type="submit"<?php if($_SESSION['role'] < 2){?> disabled <?php;} ?>>Supprimer</button>
                </form>
            </td>
        </tr>
    <?php  endforeach; ?>
    </tbody>
</table>