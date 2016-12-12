<?php

return array(
    'entity' => 'App\AppModule\Entity\Utilisateur',
    'table' => 'utilisateur',
    "primaryKey" => array('type' => 'integer', 'strategy' => 'auto_increment'),
    'properties' => array(
        'id' => array(
            'type' => 'primaryKey',
        ),
        'login' => array(),
        'nom'  => array(),
        'prenom' => array(),
        'email' => array(),
        'mdp' => array(
            'maxLength' => 100,
        ),
        'ManyToOne' => array(
            'role' => array(
                'targetEntity' => 'App\AppModule\Entity\Role',
                'foreignKey' => array( // optionnel
                    'name' => 'role_id', // opttionnel
                    'referencedColumnName' => 'id', // optionnel
                ),
            ),
            'pays' => array(
                'targetEntity' => 'App\AppModule\Entity\Pays',
                'foreignKey' => array( // optionnel
                    'name' => 'pays_id', // opttionnel
                    'referencedColumnName' => 'id', // optionnel
                )
            )
        ),
       /* 'ManyToMany' => array(
            'links' => array('targetEntity' => 'App\AppModule\Entity\Link'),
        ),*/
    )
);