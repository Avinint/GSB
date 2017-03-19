<?php

namespace Core\Container;

use Core\Component\DataBase\UnitOfWork;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class UnitOfWorkProvider implements ServiceProviderInterface
{
	public function register(Container $container)
    {
		$container['unit_of_work'] = function ($c) {
			return new UnitOfWork();
		};
	}
} 