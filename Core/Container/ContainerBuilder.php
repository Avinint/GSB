<?php

namespace Core\Container;

use Pimple\Container;

class ContainerBuilder
{
    public static function init (Container $container)
    {
        $container->register(new ConfigProvider());
		$container->register(new RouterProvider());
    }
} 