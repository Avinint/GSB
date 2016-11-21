<?php

namespace Core\Container;

use Core\Component\Database\MySQLDatabase AS Database;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DatabaseProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
		$container['db'] = function ($c) {
            return new DataBase($c['db_name'], $c['db_user'], $c['db_pass'], $c['db_host']);
        };
    }
} 