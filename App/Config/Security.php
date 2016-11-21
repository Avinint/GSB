<?php

return array(
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