<?=$form->createView(array(
        'submit' => 'modifier',
        'enctype' => 'multipart/form-data',
        'action' => $this->route->generateURL($form->getAction(), array('id' => $id))

    )); ?>
<a href="<?=$this->route->generateURL('admin_article_index');?>">Retour</a>
