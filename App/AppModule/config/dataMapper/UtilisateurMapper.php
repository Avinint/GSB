<?php

return array(
    'entity' => 'App\AppModule\Entity\Utilisateur',
    'table' => 'utilisateur',
    'properties' => array(
        'id' => array(
            'type' => 'primaryKey',
        ),
        'pseudo' => array(),
        'nom'  => array(),
        'prenom' => array(),
        'email' => array(),
        'mdp' => array(
            'length' => 100,
        ),
        'manyToMany' => array(
            'role' => array(
                'targetEntity' => 'Role',
                'foreignKey' => array( // optionnel
                    'name' => 'role_id', // opttionnel
                    'referencedColumnName' => 'id', // optionnel
                )
            )
        )
    ),

);