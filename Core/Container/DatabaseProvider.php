<?php

namespace Core\Container;

use Core\Component\Database\MySQLDatabase;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DatabaseProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['db'] = function ($c) {

            return new MySQLDataBase($c['config']->get ('db_name'), $c['config']->get('db_user'), $c['config']->get('db_pass'),
                $c['config']->get('db_host'));
        };
    }
} 