<?php

return array(
    'entity' => 'App\AppModule\Entity\Utilisateur',
    'table' => 'utilisateur',
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
                'targetEntity' => 'Role',
                'foreignKey' => array( // optionnel
                    'name' => 'role_id', // opttionnel
                    'referencedColumnName' => 'id', // optionnel
                )
            )
        )
    ),
);