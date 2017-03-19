<?php

namespace Core\Container;

use Core\Entity\EntityFactory;
use Pimple\Container;

class ContainerBuilder
{
    public static function init (Container $container)
    {
        $container->register(new ConfigProvider());
        $container->register(new KernelProvider());
		$container->register(new RouterProvider());
		$container->register(new ExceptionHandlerProvider());
		$container->register(new AuthProvider());
		$container->register(new AccessControlProvider());
		$container->register(new DatabaseProvider());
        $container->register(new CurrentRouteProvider());
		$container->register(new ToolProvider());
        $container->register(new DataMapperProvider());
        $container->register(new UnitOfWorkProvider());
        $container->register(new EntityFactoryProvider());
    }
} 