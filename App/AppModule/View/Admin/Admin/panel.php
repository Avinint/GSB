<h1>Tableau de bord</h1>
<h2>Modifier mon profil:</h2>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('utilisateur_profil_edit');?>">Editer</a></p>

<?php if(isset($_SESSION['role']) && ($_SESSION['role']> 2)): ?>
<h2>Mes articles:</h2>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('admin_sortie_add');?>">Ajouter</a></p>
<table class="article-table">
    <thead>
    <tr>
        <td class="w75p">Titre</td>
        <td>Actions</td>
    </tr>
    </thead>
    <tbody>
    <?php //var_dump($_SERVER['REQUEST_URI']);?>
    <?php  foreach($articles as $article): ?>
        <tr>
            <td><?= $article->titre ?></td>
            <td>
                <a href="<?=$this->route->generateURL('admin_article_edit', array('id' => $article->id));?>" class="btn btn-primary">Editer</a>
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
<h2>Mes annonces de sorties:</h2>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('admin_sortie_add');?>">Ajouter</a></p>
<table class="article-sorties">
    <thead>
    <tr>
        <td class="w75p">Titre</td>
        <td>Actions</td>
    </tr>
    </thead>
    <tbody>

    <?php  foreach($sorties as $article): ?>
        <tr>
            <td><?= $article->titre; ?></td>
            <td>
                <a href="<?=$this->route->generateURL('admin_sortie_edit', array('id' => $article->id));?>" class="btn btn-primary">Editer</a>
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
<h2>Mes séries:</h2>
<p><a class= "btn btn-success" href="<?=$this->route->generateURL('admin_serie_add');?>">Ajouter</a></p>
<table class="séries">
    <thead>
    <tr>
        <td class="w75p">Titre</td>
        <td>Actions</td>
    </tr>
    </thead>
    <tbody>

    <?php  foreach($series as $serie): ?>
        <tr>
            <td><?= $this->shorten($serie->titre); ?></td>
            <td>
                <a href="<?=$this->route->generateURL('admin_serie_edit', array('id' => $serie->id));?>" class="btn btn-primary">Editer</a>
                <form action="<?=$this->route->generateURL('admin_serie_delete');?>" method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $serie->id ?>">
                    <button class="btn btn-danger" type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php  endforeach; ?>
    </tbody>
</table>
&nbsp;
<?php endif ?>
<h2>Mes commentaires:</h2>
<table class="commentaires">
    <thead>
    <tr>
        <td class="w40p">Message</td>
        <td class="w35p">Date</td>
        <td>Actions</td>
    </tr>
    </thead>
    <tbody>

    <?php  foreach($commentaires as $commentaire): ?>
        <tr>
            <td><?= $this->shorten($commentaire->contenu); ?></td>
            <td><?= date_format($commentaire->date, "r"); ?></td>
            <td>
                <a href="<?=$this->route->generateURL('admin_commentaire_edit', array('id' => $commentaire->id));?>" class="btn btn-primary">Editer</a>
                <form action="<?=$this->route->generateURL('admin_commentaire_delete');?>" method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $commentaire->id ?>">
                    <button class="btn btn-danger" type="submit">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php  endforeach; ?>
    </tbody>
</table>
