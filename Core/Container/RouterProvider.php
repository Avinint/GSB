<?php

namespace Core\Container;

use Core\Component\Router\Router;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class RouterProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['router'] = function ($c) {
            return new Router();
        };
    }
} 