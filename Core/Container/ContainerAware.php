<?php

namespace Core\Container;

class ContainerAware
{
    protected $container;

    public function __construct()
    {
        $this->initContainer();
    }

    public function initContainer()
    {
        $app = \App::getInstance();
        $this->container = $app->getContainer();
    }
} 