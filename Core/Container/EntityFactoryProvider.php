<?php

namespace Core\Container;

use Core\Component\Database\MySQLDatabase;
use Core\Entity\EntityFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class EntityFactoryProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['entity_factory'] = function ($c) {

            return new EntityFactory();
        };
    }
} 