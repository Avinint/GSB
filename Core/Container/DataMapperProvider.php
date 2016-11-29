<?php

namespace Core\Container;

use Core\Component\Database\DataMapperFactory;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DataMapperProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['datamapper.factory'] = function ($c) {
            return new DataMapperFactory();
        };
    }
} 