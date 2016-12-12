<?php

return array(
    'role_hierarchy' => array(
        'ROLE_SUPER_ADMIN' => array('ROLE_ADMIN', 'ROLE_USER'),
        'ROLE_ADMIN' => array('ROLE_USER')
    ),
    'access_control' => array(
        array(
            'path' => '^/yoyo',
            'roles' => ['FREE_ACCESS'],
        ),
        array(
            'path' => '^/admin',
            'roles' => ['ROLE_ADMIN'],
        ),
        /*array(
            'path' => '^/',
            'roles' => ['ROLE_USER'],
        ),*/
	)
);