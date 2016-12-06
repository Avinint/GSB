<?php

return array(
    'entity' => 'App\AppModule\Entity\Role',
    'table' => 'role',
    "primaryKey" => array('type' => 'integer', 'strategy' => 'auto_increment'),
    'properties' => array(
        'id' => array(
            'type' => 'primaryKey',
        ),
        'nom'  => array(),
    ),
);