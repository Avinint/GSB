<?php

namespace Core\Container;

use Core\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {

        $container['paths'] = array(
            'db' => ROOT.'/App/config/dbConfig.php',
            'config' => ROOT.'/App/config/config.php',
            'security' => ROOT.'/App/config/security.php'
        );

        $container['config'] = function ($c) {
            return Config::getInstance($c['paths']['db'], $c['paths']['config'], $c['paths']['security']);
        };

    }
} 