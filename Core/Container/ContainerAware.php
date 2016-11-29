<?php

namespace Core\Container;


class ContainerAware
{
    protected $container;

    public function initContainer()
    {
        $app = \App::getInstance();
        $this->container = $app->getContainer();
    }
} 