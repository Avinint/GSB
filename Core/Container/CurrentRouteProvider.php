<?php

namespace Core\Container;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CurrentRouteProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['current_route'] = function ($c) {

            return $c['router']->getCurrentRoute();
        };
    }
} 