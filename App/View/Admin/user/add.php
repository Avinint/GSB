<?=$form->createView(array(
        'submit' => 'créer',
        'enctype' => 'multipart/form-data',
        'action' => $this->route->generateURL($form->getAction(), array('id' => $id))

    )); ?>

<a href="<?=$this->route->generateURL('admin_utilisateur_index');?>">Retour</a>
