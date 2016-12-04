<?php

return array(
    'entity' => 'App\AppModule\Entity\Continent',
    'table' => 'continent',
    "primaryKey" => array('type' => 'integer', 'strategy' => 'auto_increment'),
    'properties' => array(
        'id' => array(
            'type' => 'primaryKey',
        ),
        'nom'  => array(),
    ),
);