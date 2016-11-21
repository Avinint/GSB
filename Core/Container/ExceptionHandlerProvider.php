<?php

namespace Core\Container;

use Core\Component\Exception\ExceptionHandler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ExceptionHandlerProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['exception_handler'] = function ($c) {
            ExceptionHandler::register($c['env']);
        };
    }
} 