<?php

return array(
    'entity' => 'App\AppModule\Entity\Pays',
    'table' => 'pays',
    "primaryKey" => array('type' => 'integer', 'strategy' => 'auto_increment'),
    'properties' => array(
        'id' => array(
            'type' => 'primaryKey',
        ),
        'nom'  => array(),

        'ManyToOne' => array(
             'continent' => array(
                'targetEntity' => 'App\AppModule\Entity\Continent',
                'foreignKey' => array( // optionnel
                    'name' => 'continent_id', // opttionnel
                    'referencedColumnName' => 'id', // optionnel
                )
            )
        )
    ),
); 