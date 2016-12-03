<?php

namespace Core\Container;

use Core\Table\Table;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class KernelProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['app'] = function ($c) {

            return $c['kernel'];
        };
    }
} 