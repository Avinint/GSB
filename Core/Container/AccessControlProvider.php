<?php

namespace Core\Container;

use Core\Component\Auth\DbAuth;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AccessControlProvider implements ServiceProviderInterface
{
	public function register(Container $container)
    {
		$container['access_control'] = function ($c) {
			return $c['config']->get('access_control');
		};
	}
} 