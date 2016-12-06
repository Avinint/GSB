<?php

namespace Core\Container;

use Core\Component\Tool\Tool;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ToolProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['tool'] = function ($c) {
            return new Tool();
        };
    }
} 