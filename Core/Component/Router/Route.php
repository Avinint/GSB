<?php

namespace Core\Component\Router;

class Route
{
    private $name;
    private $path;
    private $controller;
    private $parameters;

    function __construct($name = null, $path = null, $parameters = null)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->path = $path;
    }

    /**
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return String
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param String $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return String
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param String $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return String
     */
    public function getPath()
    {
        return $this->path;
    }


    public function __toString()
    {
        return $this->getName();
    }
}