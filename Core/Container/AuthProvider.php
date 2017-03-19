<?php

namespace Core\Container;

use Core\Component\Auth\Auth;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AuthProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['auth'] = function ($c) {
<<<<<<< HEAD
            return new Auth();
=======
            return new DbAuth($c['config']);
>>>>>>> 2f3ee2e6c024bc130aa565e3030547823e800c3d
        };
    }
} 